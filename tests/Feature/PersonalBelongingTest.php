<?php

namespace Tests\Feature;

use App\Models\PersonalBelonging;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalBelongingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_personal_belongings()
    {
        $response = $this->get(route('personal-belongings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_own_personal_belongings()
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $response = $this->get(route('personal-belongings.index'));
        $response->assertOk();
    }

    public function test_user_can_create_personal_belonging()
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $payload = [
            'name' => 'Jaket KKN Biru',
            'quantity' => 1,
            'unit' => 'pcs',
            'notes' => 'Disimpan di koper',
        ];

        $response = $this->post(route('personal-belongings.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('personal_belongings', [
            'user_id' => $user->id,
            'name' => 'Jaket KKN Biru',
            'quantity' => 1,
            'unit' => 'pcs',
            'notes' => 'Disimpan di koper',
            'is_packed_departure' => false,
            'is_packed_return' => false,
        ]);
    }

    public function test_user_can_update_own_personal_belonging()
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $belonging = PersonalBelonging::create([
            'user_id' => $user->id,
            'name' => 'Laptop ASUS',
            'quantity' => 1,
            'unit' => 'unit',
            'notes' => 'Tas ransel',
        ]);

        $payload = [
            'name' => 'Laptop ASUS ROG',
            'quantity' => 2,
            'unit' => 'unit',
            'notes' => 'Tas ransel hitam',
        ];

        $response = $this->put(route('personal-belongings.update', $belonging), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('personal_belongings', [
            'id' => $belonging->id,
            'name' => 'Laptop ASUS ROG',
            'quantity' => 2,
            'unit' => 'unit',
            'notes' => 'Tas ransel hitam',
        ]);
    }

    public function test_user_cannot_update_other_users_personal_belonging()
    {
        $user1 = User::factory()->create(['role' => 'host']);
        $user2 = User::factory()->create(['role' => 'host']);

        $belonging = PersonalBelonging::create([
            'user_id' => $user1->id,
            'name' => 'Laptop ASUS',
            'quantity' => 1,
            'unit' => 'unit',
        ]);

        $this->actingAs($user2);

        $payload = [
            'name' => 'Hack Laptop',
            'quantity' => 5,
            'unit' => 'unit',
        ];

        $response = $this->put(route('personal-belongings.update', $belonging), $payload);
        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_personal_belonging()
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $belonging = PersonalBelonging::create([
            'user_id' => $user->id,
            'name' => 'Sabun Mandi',
            'quantity' => 3,
            'unit' => 'pcs',
        ]);

        $response = $this->delete(route('personal-belongings.destroy', $belonging));
        $response->assertRedirect();

        $this->assertDatabaseMissing('personal_belongings', ['id' => $belonging->id]);
    }

    public function test_user_cannot_delete_other_users_personal_belonging()
    {
        $user1 = User::factory()->create(['role' => 'host']);
        $user2 = User::factory()->create(['role' => 'host']);

        $belonging = PersonalBelonging::create([
            'user_id' => $user1->id,
            'name' => 'Sabun Mandi',
            'quantity' => 3,
            'unit' => 'pcs',
        ]);

        $this->actingAs($user2);

        $response = $this->delete(route('personal-belongings.destroy', $belonging));
        $response->assertStatus(403);
    }

    public function test_user_can_toggle_packed_status()
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $belonging = PersonalBelonging::create([
            'user_id' => $user->id,
            'name' => 'Sleeping Bag',
            'quantity' => 1,
            'unit' => 'pcs',
            'is_packed_departure' => false,
            'is_packed_return' => false,
        ]);

        // Toggle departure packing
        $response = $this->post(route('personal-belongings.toggle-packed', $belonging), ['type' => 'departure']);
        $response->assertRedirect();
        $this->assertTrue($belonging->fresh()->is_packed_departure);

        // Toggle return packing
        $response = $this->post(route('personal-belongings.toggle-packed', $belonging), ['type' => 'return']);
        $response->assertRedirect();
        $this->assertTrue($belonging->fresh()->is_packed_return);
    }
}
