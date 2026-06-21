<?php

namespace Tests\Feature;

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

        $image = UploadedFile::fake()->image('wajan.jpg');

        $payload = [
            'name' => 'Wajan Goreng',
            'quantity' => 2,
            'condition' => 'good',
            'notes' => 'Milik warga RT 01',
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
            'name' => 'Wajan Goreng',
            'quantity' => 2,
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
            'condition' => 'good',
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
                'condition' => 'good',
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

        $oldImage = UploadedFile::fake()->image('old.jpg');
        $oldImagePath = Storage::disk('public')->putFile('inventories', $oldImage);

        $inventory = Inventory::create([
            'host_id' => $host->id,
            'name' => 'Lama',
            'quantity' => 1,
            'condition' => 'good',
            'image_path' => $oldImagePath,
        ]);

        $newImage = UploadedFile::fake()->image('new.jpg');

        $payload = [
            'name' => 'Baru',
            'quantity' => 3,
            'condition' => 'damaged',
            'notes' => 'Gagang patah',
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
            'condition' => 'good',
        ]);

        $this->actingAs($host2);

        $payload = [
            'name' => 'Hack',
            'quantity' => 10,
            'condition' => 'lost',
        ];

        $response = $this->put(route('management.inventory.update', $inventory), $payload);
        $response->assertStatus(403);
    }

    public function test_host_can_delete_inventory()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $image = UploadedFile::fake()->image('item.jpg');
        $imagePath = Storage::disk('public')->putFile('inventories', $image);

        $inventory = Inventory::create([
            'host_id' => $host->id,
            'name' => 'Hapus Saya',
            'quantity' => 1,
            'condition' => 'good',
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
                ]
            ]
        ];

        $response = $this->post(route('management.logistic.checkout'), $payload);
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

    public function test_user_cannot_checkout_more_than_available_stock()
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
                ]
            ]
        ];

        $response = $this->post(route('management.logistic.checkout'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['items']);
    }
}
