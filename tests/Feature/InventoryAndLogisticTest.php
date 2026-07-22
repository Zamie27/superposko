<?php

namespace Tests\Feature;

use App\Models\Finance;
use App\Models\Inventory;
use App\Models\Logistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryAndLogisticTest extends TestCase
{
    use RefreshDatabase;

    // --- INVENTORY TESTS ---

    public function test_guests_cannot_access_inventory()
    {
        $response = $this->get(route('management.inventory.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_inventory()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('management.inventory.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_hosts_can_access_inventory()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('management.inventory.index'));
        $response->assertOk();
    }

    public function test_host_can_create_inventory_with_owner_and_image()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);

        $image = UploadedFile::fake()->image('wajan.png');

        $payload = [
            'name' => 'Wajan Goreng',
            'quantity' => 2,
            'unit' => 'pcs',
            'condition' => 'good',
            'notes' => 'Milik warga RT 01',
            'source' => 'member',
            'owner_id' => $member->id,
            'image' => $image,
        ];

        $response = $this->post(route('management.inventory.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $inventory = Inventory::first();
        $this->assertNotNull($inventory->image_path);
        Storage::disk('public')->assertExists($inventory->image_path);

        $this->assertDatabaseHas('inventories', [
            'host_id' => $host->id,
            'owner_id' => $member->id,
            'source' => 'member',
            'name' => 'Wajan Goreng',
            'quantity' => 2,
            'unit' => 'pcs',
            'condition' => 'good',
            'notes' => 'Milik warga RT 01',
        ]);
    }

    public function test_non_finance_member_cannot_create_inventory()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $payload = [
            'name' => 'Tikar',
            'quantity' => 5,
            'unit' => 'pcs',
            'condition' => 'good',
            'source' => 'member',
        ];

        $response = $this->post(route('management.inventory.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_finance_roles_can_create_inventory()
    {
        $host = User::factory()->create(['role' => 'host']);

        $roles = ['sekretaris', 'bendahara'];
        foreach ($roles as $role) {
            $member = User::factory()->create([
                'role' => $role,
                'host_id' => $host->id,
            ]);
            $this->actingAs($member);

            $payload = [
                'name' => "Tikar {$role}",
                'quantity' => 1,
                'unit' => 'pcs',
                'condition' => 'good',
                'source' => 'member',
            ];

            $response = $this->post(route('management.inventory.store'), $payload);
            $response->assertRedirect();
            $response->assertSessionHasNoErrors();

            $this->assertDatabaseHas('inventories', [
                'name' => "Tikar {$role}",
            ]);
        }
    }

    public function test_host_can_update_inventory()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $oldImage = UploadedFile::fake()->image('old.png');
        $oldImagePath = Storage::disk('public')->putFile('inventories', $oldImage);

        $inventory = Inventory::create([
            'host_id' => $host->id,
            'name' => 'Lama',
            'quantity' => 1,
            'unit' => 'pcs',
            'condition' => 'good',
            'source' => 'member',
            'image_path' => $oldImagePath,
        ]);

        $newImage = UploadedFile::fake()->image('new.png');

        $payload = [
            'name' => 'Baru',
            'quantity' => 3,
            'unit' => 'pcs',
            'condition' => 'damaged',
            'notes' => 'Gagang patah',
            'source' => 'member',
            'image' => $newImage,
        ];

        $response = $this->put(route('management.inventory.update', $inventory), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $inventory->refresh();
        $this->assertNotEquals($oldImagePath, $inventory->image_path);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($inventory->image_path);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'name' => 'Baru',
            'quantity' => 3,
            'unit' => 'pcs',
            'condition' => 'damaged',
        ]);
    }

    public function test_host_cannot_update_other_hosts_inventory()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $inventory = Inventory::create([
            'host_id' => $host1->id,
            'name' => 'Barang Host 1',
            'quantity' => 1,
            'unit' => 'pcs',
            'condition' => 'good',
            'source' => 'member',
        ]);

        $this->actingAs($host2);

        $payload = [
            'name' => 'Hack',
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'lost',
            'source' => 'member',
        ];

        $response = $this->put(route('management.inventory.update', $inventory), $payload);
        $response->assertStatus(403);
    }

    public function test_host_can_delete_inventory()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $image = UploadedFile::fake()->image('item.png');
        $imagePath = Storage::disk('public')->putFile('inventories', $image);

        $inventory = Inventory::create([
            'host_id' => $host->id,
            'name' => 'Hapus Saya',
            'quantity' => 1,
            'unit' => 'pcs',
            'condition' => 'good',
            'source' => 'member',
            'image_path' => $imagePath,
        ]);

        $response = $this->delete(route('management.inventory.destroy', $inventory));
        $response->assertRedirect();

        Storage::disk('public')->assertMissing($imagePath);
        $this->assertDatabaseMissing('inventories', [
            'id' => $inventory->id,
        ]);
    }

    // --- LOGISTIC TESTS ---

    public function test_guests_cannot_access_logistic()
    {
        $response = $this->get(route('management.logistic.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_logistic()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('management.logistic.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_hosts_can_access_logistic()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('management.logistic.index'));
        $response->assertOk();
    }

    public function test_host_can_create_logistic_with_split_quantity()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $payload = [
            'name' => 'Beras',
            'quantity' => 12.5,
            'unit' => 'kg',
            'status' => 'sufficient',
            'notes' => 'Di dapur',
            'source' => 'member',
        ];

        $response = $this->post(route('management.logistic.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('logistics', [
            'host_id' => $host->id,
            'name' => 'Beras',
            'quantity' => 12.5,
            'unit' => 'kg',
            'status' => 'sufficient',
            'notes' => 'Di dapur',
        ]);
    }

    public function test_non_finance_member_cannot_create_logistic()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $payload = [
            'name' => 'Telur',
            'quantity' => 1,
            'unit' => 'Rak',
            'status' => 'sufficient',
            'source' => 'member',
        ];

        $response = $this->post(route('management.logistic.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_host_can_update_logistic()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $logistic = Logistic::create([
            'host_id' => $host->id,
            'name' => 'Beras',
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'sufficient',
        ]);

        $payload = [
            'name' => 'Beras Pandan Wangi',
            'quantity' => 2.5,
            'unit' => 'kg',
            'status' => 'low',
            'notes' => 'Hampir habis',
            'source' => 'member',
        ];

        $response = $this->put(route('management.logistic.update', $logistic), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('logistics', [
            'id' => $logistic->id,
            'name' => 'Beras Pandan Wangi',
            'quantity' => 2.5,
            'unit' => 'kg',
            'status' => 'low',
        ]);
    }

    public function test_host_cannot_update_other_hosts_logistic()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $logistic = Logistic::create([
            'host_id' => $host1->id,
            'name' => 'Beras Host 1',
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'sufficient',
        ]);

        $this->actingAs($host2);

        $payload = [
            'name' => 'Hack Beras',
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'out',
            'source' => 'member',
        ];

        $response = $this->put(route('management.logistic.update', $logistic), $payload);
        $response->assertStatus(403);
    }

    public function test_host_can_delete_logistic()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $logistic = Logistic::create([
            'host_id' => $host->id,
            'name' => 'Hapus Saja',
            'quantity' => 5,
            'unit' => 'Bks',
            'status' => 'sufficient',
        ]);

        $response = $this->delete(route('management.logistic.destroy', $logistic));
        $response->assertRedirect();

        $this->assertDatabaseMissing('logistics', [
            'id' => $logistic->id,
        ]);
    }

    public function test_user_can_perform_batch_checkout_logistics()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $item1 = Logistic::create([
            'host_id' => $host->id,
            'name' => 'Beras',
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'sufficient',
        ]);

        $item2 = Logistic::create([
            'host_id' => $host->id,
            'name' => 'Minyak',
            'quantity' => 5,
            'unit' => 'liter',
            'status' => 'sufficient',
        ]);

        $payload = [
            'items' => [
                [
                    'id' => $item1->id,
                    'amount' => 8, // leaves 2 (low)
                ],
                [
                    'id' => $item2->id,
                    'amount' => 5, // leaves 0 (out)
                ],
            ],
        ];

        $response = $this->post(route('management.logistic.barang-keluar'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('logistics', [
            'id' => $item1->id,
            'quantity' => 2,
            'status' => 'low',
        ]);

        $this->assertDatabaseHas('logistics', [
            'id' => $item2->id,
            'quantity' => 0,
            'status' => 'out',
        ]);
    }

    public function test_user_cannot_record_barang_keluar_more_than_available_stock()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $item = Logistic::create([
            'host_id' => $host->id,
            'name' => 'Beras',
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'sufficient',
        ]);

        $payload = [
            'items' => [
                [
                    'id' => $item->id,
                    'amount' => 15,
                ],
            ],
        ];

        $response = $this->post(route('management.logistic.barang-keluar'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['items']);
    }

    public function test_host_can_create_inventory_purchased_from_kas()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $payload = [
            'name' => 'Kipas Angin',
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'good',
            'source' => 'purchase',
            'purchase_price' => 250000,
        ];

        $response = $this->post(route('management.inventory.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Inventory record created with correct source
        $this->assertDatabaseHas('inventories', [
            'host_id' => $host->id,
            'source' => 'purchase',
            'purchase_price' => 250000,
            'name' => 'Kipas Angin',
        ]);

        // Finance expense record auto-created in E-Bendahara (2 * 250,000 = 500,000)
        $this->assertDatabaseHas('finances', [
            'host_id' => $host->id,
            'type' => 'expense',
            'amount' => 500000,
        ]);

        // Inventory finance_id links to the created Finance record
        $inventory = Inventory::where('name', 'Kipas Angin')->first();
        $this->assertNotNull($inventory->finance_id);
        $this->assertDatabaseHas('finances', ['id' => $inventory->finance_id]);
    }

    public function test_deleting_purchased_inventory_also_deletes_finance_record()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        // Create a finance record and link it to inventory
        $finance = Finance::create([
            'host_id' => $host->id,
            'program_kerja_id' => null,
            'created_by' => $host->id,
            'type' => 'expense',
            'amount' => 150000,
            'title' => 'Pembelian Inventaris: Dispenser',
            'description' => 'Test',
            'date' => now()->toDateString(),
        ]);

        $inventory = Inventory::create([
            'host_id' => $host->id,
            'owner_id' => null,
            'source' => 'purchase',
            'purchase_price' => 150000,
            'finance_id' => $finance->id,
            'name' => 'Dispenser',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'good',
        ]);

        $response = $this->delete(route('management.inventory.destroy', $inventory->id));
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Both inventory and finance record should be deleted
        $this->assertDatabaseMissing('inventories', ['id' => $inventory->id]);
        $this->assertDatabaseMissing('finances', ['id' => $finance->id]);
    }

    public function test_host_can_create_logistic_purchased_from_kas()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $payload = [
            'name' => 'Telur Ayam',
            'quantity' => 3,
            'unit' => 'rak',
            'status' => 'sufficient',
            'source' => 'purchase',
            'purchase_price' => 60000, // 60,000 per rak
        ];

        $response = $this->post(route('management.logistic.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // 3 * 60,000 = 180,000 total expense
        $this->assertDatabaseHas('logistics', [
            'host_id' => $host->id,
            'source' => 'purchase',
            'purchase_price' => 60000,
            'name' => 'Telur Ayam',
        ]);

        $this->assertDatabaseHas('finances', [
            'host_id' => $host->id,
            'type' => 'expense',
            'amount' => 180000,
        ]);

        $logistic = Logistic::where('name', 'Telur Ayam')->first();
        $this->assertNotNull($logistic->finance_id);
    }

    public function test_deleting_purchased_logistic_also_deletes_finance_record()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $finance = Finance::create([
            'host_id' => $host->id,
            'program_kerja_id' => null,
            'created_by' => $host->id,
            'type' => 'expense',
            'amount' => 120000,
            'title' => 'Pembelian Logistik: Telur Ayam',
            'description' => 'Test',
            'date' => now()->toDateString(),
        ]);

        $logistic = Logistic::create([
            'host_id' => $host->id,
            'source' => 'purchase',
            'purchase_price' => 60000,
            'finance_id' => $finance->id,
            'name' => 'Telur Ayam',
            'quantity' => 2,
            'unit' => 'rak',
            'status' => 'sufficient',
        ]);

        $response = $this->delete(route('management.logistic.destroy', $logistic->id));
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('logistics', ['id' => $logistic->id]);
        $this->assertDatabaseMissing('finances', ['id' => $finance->id]);
    }

    public function test_host_can_restock_existing_logistic_item_and_creates_new_finance_record()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        // Initial purchase: 1 kg Wortel (Rp 10.000)
        $initialFinance = Finance::create([
            'host_id' => $host->id,
            'program_kerja_id' => null,
            'created_by' => $host->id,
            'type' => 'expense',
            'amount' => 10000,
            'payment_method' => 'Cash',
            'title' => 'Pembelian Logistik: Wortel',
            'description' => 'Awal',
            'date' => now()->toDateString(),
        ]);

        $logistic = Logistic::create([
            'host_id' => $host->id,
            'source' => 'purchase',
            'purchase_price' => 10000,
            'finance_id' => $initialFinance->id,
            'name' => 'Wortel',
            'quantity' => 1,
            'unit' => 'kg',
            'status' => 'sufficient',
            'date' => now()->toDateString(),
        ]);

        // Restock: Buy 5 kg Wortel at 10.000/kg (50.000) using Kas
        $payload = [
            'logistic_id' => $logistic->id,
            'name' => 'Wortel',
            'quantity' => 5,
            'unit' => 'kg',
            'status' => 'sufficient',
            'source' => 'purchase',
            'purchase_price' => 10000,
            'payment_method' => 'Cash',
            'date' => now()->toDateString(),
        ];

        $response = $this->post(route('management.logistic.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // 1. Logistic card should not be duplicated (total count = 1), quantity updated to 6
        $this->assertEquals(1, Logistic::where('host_id', $host->id)->where('name', 'Wortel')->count());

        $logistic->refresh();
        $this->assertEquals(6.0, (float) $logistic->quantity);

        // 2. Initial finance record remains untouched (Rp 10.000)
        $this->assertDatabaseHas('finances', [
            'id' => $initialFinance->id,
            'amount' => 10000,
        ]);

        // 3. New finance record created for this new purchase (5 kg * 10,000 = Rp 50.000)
        $this->assertDatabaseHas('finances', [
            'host_id' => $host->id,
            'type' => 'expense',
            'amount' => 50000,
            'payment_method' => 'Cash',
            'title' => 'Pembelian Logistik: Wortel',
        ]);

        $this->assertEquals(2, Finance::where('host_id', $host->id)->where('type', 'expense')->count());
    }
}
