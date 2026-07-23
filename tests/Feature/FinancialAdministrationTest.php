<?php

namespace Tests\Feature;

use App\Models\Finance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_financial_administration_modules()
    {
        $response = $this->get(route('financial-admin.buku-kas-umum'));
        $response->assertRedirect(route('login'));
    }

    public function test_subscribed_hosts_can_access_all_seven_financial_admin_modules()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        // Seed sample finance data
        Finance::create([
            'host_id' => $host->id,
            'created_by' => $host->id,
            'type' => 'income',
            'amount' => 500000,
            'payment_method' => 'Cash',
            'title' => 'Iuran Awal Posko',
            'category' => 'Iuran Anggota',
            'date' => now()->toDateString(),
        ]);

        Finance::create([
            'host_id' => $host->id,
            'created_by' => $host->id,
            'type' => 'expense',
            'amount' => 150000,
            'payment_method' => 'Cash',
            'title' => 'Belanja Galon Air',
            'category' => 'Konsumsi',
            'date' => now()->toDateString(),
        ]);

        // Test 1: Buku Kas Umum
        $r1 = $this->get(route('financial-admin.buku-kas-umum'));
        $r1->assertOk();

        // Test 2: Buku Penerimaan Dana
        $r2 = $this->get(route('financial-admin.buku-penerimaan'));
        $r2->assertOk();

        // Test 3: Buku Pengeluaran Dana
        $r3 = $this->get(route('financial-admin.buku-pengeluaran'));
        $r3->assertOk();

        // Test 4: Bukti Pembayaran
        $r4 = $this->get(route('financial-admin.bukti-pembayaran'));
        $r4->assertOk();

        // Test 5: Kwitansi
        $r5 = $this->get(route('financial-admin.kwitansi'));
        $r5->assertOk();

        // Test 6: Nota Belanja
        $r6 = $this->get(route('financial-admin.nota-belanja'));
        $r6->assertOk();

        // Test 7: LPJ Keuangan
        $r7 = $this->get(route('financial-admin.lpj-keuangan'));
        $r7->assertOk();
    }
}
