<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_contacts()
    {
        $response = $this->get(route('contacts.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_contacts()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('contacts.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_hosts_can_access_contacts()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('contacts.index'));
        $response->assertOk();
    }

    public function test_host_can_create_contact()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $payload = [
            'name' => 'Budi Santoso',
            'role_title' => 'Kepala Desa',
            'category' => 'aparat_desa',
            'phone' => '081234567890',
            'email' => 'budi@desa.id',
            'notes' => 'Hanya dihubungi jam kerja',
        ];

        $response = $this->post(route('contacts.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'host_id' => $host->id,
            'name' => 'Budi Santoso',
            'role_title' => 'Kepala Desa',
            'category' => 'aparat_desa',
            'phone' => '6281234567890', // verified format normalization
            'email' => 'budi@desa.id',
        ]);
    }

    public function test_host_can_update_contact()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $contact = Contact::create([
            'host_id' => $host->id,
            'name' => 'Lama',
            'role_title' => 'Staf',
            'category' => 'aparat_desa',
        ]);

        $payload = [
            'name' => 'Baru',
            'role_title' => 'Sekretaris',
            'category' => 'aparat_desa',
            'phone' => '08123',
            'email' => 'baru@desa.id',
            'notes' => 'Baru',
        ];

        $response = $this->put(route('contacts.update', $contact), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Baru',
            'role_title' => 'Sekretaris',
        ]);
    }

    public function test_host_cannot_update_other_hosts_contact()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $contact = Contact::create([
            'host_id' => $host1->id,
            'name' => 'Kontak Host 1',
            'role_title' => 'Staf',
            'category' => 'aparat_desa',
        ]);

        $this->actingAs($host2);

        $payload = [
            'name' => 'Maling',
            'role_title' => 'Maling',
            'category' => 'aparat_desa',
        ];

        $response = $this->put(route('contacts.update', $contact), $payload);
        $response->assertStatus(403);
    }

    public function test_host_can_delete_contact()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $contact = Contact::create([
            'host_id' => $host->id,
            'name' => 'Mau Dihapus',
            'role_title' => 'Staf',
            'category' => 'aparat_desa',
        ]);

        $response = $this->delete(route('contacts.destroy', $contact));
        $response->assertRedirect();

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
