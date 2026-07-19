<?php

namespace Tests\Feature;

use App\Models\Finance;
use App\Models\FinanceTag;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_finance_page()
    {
        $response = $this->get(route('finance.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_finance_page()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('finance.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_members_can_view_finance_page()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $response = $this->get(route('finance.index'));
        $response->assertOk();
    }

    public function test_bendahara_can_create_finance_transaction()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create([
            'role' => 'bendahara',
            'host_id' => $host->id,
        ]);
        $this->actingAs($bendahara);

        $receipt = UploadedFile::fake()->image('nota.png');

        $payload = [
            'type' => 'expense',
            'amount' => 150000,
            'title' => 'Beli Cat untuk Plangisasi',
            'description' => 'Membeli cat 3 kaleng warna biru',
            'date' => '2026-06-22',
            'receipt_file' => $receipt,
        ];

        $response = $this->post(route('finance.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $transaction = Finance::first();
        $this->assertNotNull($transaction->receipt_path);
        Storage::disk('public')->assertExists($transaction->receipt_path);

        $this->assertDatabaseHas('finances', [
            'host_id' => $host->id,
            'created_by' => $bendahara->id,
            'type' => 'expense',
            'amount' => 150000,
            'title' => 'Beli Cat untuk Plangisasi',
        ]);
    }

    public function test_ordinary_member_cannot_create_finance_transaction()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $payload = [
            'type' => 'income',
            'amount' => 100000,
            'title' => 'Iuran Kas Mingguan',
            'date' => '2026-06-22',
        ];

        $response = $this->post(route('finance.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_posko_isolation_on_finance()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $bendahara1 = User::factory()->create(['role' => 'bendahara', 'host_id' => $host1->id]);

        $host2 = User::factory()->create(['role' => 'host']);
        $bendahara2 = User::factory()->create(['role' => 'bendahara', 'host_id' => $host2->id]);

        // Host 1 records income
        Finance::create([
            'host_id' => $host1->id,
            'created_by' => $bendahara1->id,
            'type' => 'income',
            'amount' => 500000,
            'title' => 'Uang Kas Awal',
            'date' => '2026-06-22',
        ]);

        $this->actingAs($bendahara2);
        $response = $this->get(route('finance.index'));
        $response->assertOk();

        // Finance transactions of Host 1 must not be visible to Host 2
        $response->assertInertia(fn ($page) => $page
            ->component('finance/Index')
            ->has('finances', 0)
        );
    }

    public function test_linking_finance_expense_to_proker_updates_spent()
    {
        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create(['role' => 'bendahara', 'host_id' => $host->id]);
        $this->actingAs($bendahara);

        $proker = ProgramKerja::create([
            'host_id' => $host->id,
            'name' => 'Fisik Plang Jalan',
            'category' => 'fisik',
            'budget' => 200000,
            'status' => 'planned',
        ]);

        $payload = [
            'type' => 'expense',
            'amount' => 125000,
            'title' => 'Beli Kayu dan Paku',
            'date' => '2026-06-22',
            'program_kerja_id' => $proker->id,
        ];

        $response = $this->post(route('finance.store'), $payload);
        $response->assertRedirect();

        // Verify finance relation exists
        $this->assertDatabaseHas('finances', [
            'program_kerja_id' => $proker->id,
            'amount' => 125000,
        ]);

        // Verify Logbook controller indexes the spent budget properly
        $response2 = $this->get(route('logbook.index'));
        $response2->assertOk();

        // Assert the returned program kerja has spent field populated
        $prokers = $response2->original->getData()['page']['props']['programKerjas'];
        $this->assertEquals(125000, $prokers[0]['spent']);
    }

    public function test_bendahara_can_create_custom_tag()
    {
        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create(['role' => 'bendahara', 'host_id' => $host->id]);
        $this->actingAs($bendahara);

        $response = $this->post(route('finance.tags.store'), [
            'name' => 'Sewa Alat',
            'type' => 'expense',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('finance_tags', [
            'host_id' => $host->id,
            'name' => 'Sewa Alat',
            'type' => 'expense',
        ]);
    }

    public function test_custom_tags_are_isolated_between_groups()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $bendahara1 = User::factory()->create(['role' => 'bendahara', 'host_id' => $host1->id]);

        $host2 = User::factory()->create(['role' => 'host']);
        $bendahara2 = User::factory()->create(['role' => 'bendahara', 'host_id' => $host2->id]);

        // Host 1 creates a custom tag
        $this->actingAs($bendahara1);
        $this->post(route('finance.tags.store'), [
            'name' => 'Sewa Sound System',
            'type' => 'expense',
        ]);

        // Host 2 should not see Host 1's tags
        $this->actingAs($bendahara2);
        $response = $this->get(route('finance.index'));
        $response->assertOk();

        $customTags = $response->original->getData()['page']['props']['customTags'];
        $this->assertCount(0, $customTags);
    }

    public function test_bendahara_can_delete_own_custom_tag()
    {
        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create(['role' => 'bendahara', 'host_id' => $host->id]);
        $this->actingAs($bendahara);

        $tag = FinanceTag::create([
            'host_id' => $host->id,
            'name' => 'Sewa Alat',
            'type' => 'expense',
        ]);

        $response = $this->delete(route('finance.tags.destroy', $tag));
        $response->assertRedirect();

        $this->assertDatabaseMissing('finance_tags', ['id' => $tag->id]);
    }

    public function test_allocation_from_seabank_deducts_seabank_balance()
    {
        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create(['role' => 'bendahara', 'host_id' => $host->id]);
        $this->actingAs($bendahara);

        // Add income to SeaBank
        Finance::create([
            'host_id' => $host->id,
            'created_by' => $bendahara->id,
            'type' => 'income',
            'payment_method' => 'SeaBank',
            'amount' => 500000,
            'title' => 'Iuran via SeaBank',
            'date' => '2026-06-22',
        ]);

        $proker = ProgramKerja::create([
            'host_id' => $host->id,
            'name' => 'Proker Test SeaBank',
            'category' => 'fisik',
            'budget' => 300000,
            'status' => 'planned',
        ]);

        // Allocate from SeaBank to Proker
        $response = $this->post(route('finance.store'), [
            'type' => 'allocation',
            'payment_method' => 'SeaBank',
            'amount' => 200000,
            'title' => 'Alokasi Dana dari SeaBank',
            'date' => '2026-06-22',
            'program_kerja_id' => $proker->id,
            'category' => 'Kas ke Proker',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Check SeaBank balance decreased
        $financeResponse = $this->get(route('finance.index'));
        $balances = $financeResponse->original->getData()['page']['props']['metrics']['balances_by_method'];
        $this->assertEquals(300000, $balances['SeaBank']);
    }

    public function test_allocation_fails_when_specific_method_balance_insufficient()
    {
        $host = User::factory()->create(['role' => 'host']);
        $bendahara = User::factory()->create(['role' => 'bendahara', 'host_id' => $host->id]);
        $this->actingAs($bendahara);

        // Add income only to Cash
        Finance::create([
            'host_id' => $host->id,
            'created_by' => $bendahara->id,
            'type' => 'income',
            'payment_method' => 'Cash',
            'amount' => 500000,
            'title' => 'Iuran Tunai',
            'date' => '2026-06-22',
        ]);

        $proker = ProgramKerja::create([
            'host_id' => $host->id,
            'name' => 'Proker Test Balance',
            'category' => 'fisik',
            'budget' => 300000,
            'status' => 'planned',
        ]);

        // Try to allocate from SeaBank (which has 0 balance)
        $response = $this->post(route('finance.store'), [
            'type' => 'allocation',
            'payment_method' => 'SeaBank',
            'amount' => 100000,
            'title' => 'Alokasi dari SeaBank Kosong',
            'date' => '2026-06-22',
            'program_kerja_id' => $proker->id,
            'category' => 'Kas ke Proker',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseMissing('finances', ['title' => 'Alokasi dari SeaBank Kosong']);
    }
}
