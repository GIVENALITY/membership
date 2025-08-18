<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_impersonate_manager()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create a manager user
        $manager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Start impersonation
        $response = $this->get(route('impersonate.start', $manager->id));

        // Should redirect to dashboard
        $response->assertRedirect(route('dashboard'));

        // Should be logged in as manager
        $this->assertAuthenticatedAs($manager);

        // Should have impersonation session data
        $this->assertSessionHas('impersonator_id', $superadmin->id);
        $this->assertSessionHas('impersonator_name', $superadmin->name);
        $this->assertSessionHas('impersonator_email', $superadmin->email);
    }

    public function test_superadmin_cannot_impersonate_another_superadmin()
    {
        // Create a superadmin user
        $superadmin1 = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create another superadmin user
        $superadmin2 = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Login as first superadmin
        $this->actingAs($superadmin1);

        // Try to impersonate second superadmin
        $response = $this->get(route('impersonate.start', $superadmin2->id));

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_non_superadmin_cannot_impersonate()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a manager user
        $manager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Create another manager user
        $targetManager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Login as manager
        $this->actingAs($manager);

        // Try to impersonate another manager
        $response = $this->get(route('impersonate.start', $targetManager->id));

        // Should get 403 error
        $response->assertStatus(403);
    }

    public function test_can_stop_impersonation()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create a manager user
        $manager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Start impersonation
        $this->get(route('impersonate.start', $manager->id));

        // Should be logged in as manager
        $this->assertAuthenticatedAs($manager);

        // Stop impersonation
        $response = $this->get(route('impersonate.stop'));

        // Should redirect to superadmin dashboard
        $response->assertRedirect(route('superadmin.dashboard'));

        // Should be logged in as superadmin again
        $this->assertAuthenticatedAs($superadmin);

        // Should not have impersonation session data
        $this->assertSessionMissing('impersonator_id');
        $this->assertSessionMissing('impersonator_name');
        $this->assertSessionMissing('impersonator_email');
    }

    public function test_impersonation_status_endpoint()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create a manager user
        $manager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Check status before impersonation
        $response = $this->getJson(route('impersonate.status'));
        $response->assertJson([
            'is_impersonating' => false,
        ]);

        // Start impersonation
        $this->get(route('impersonate.start', $manager->id));

        // Check status during impersonation
        $response = $this->getJson(route('impersonate.status'));
        $response->assertJson([
            'is_impersonating' => true,
            'impersonator_name' => $superadmin->name,
            'impersonator_email' => $superadmin->email,
        ]);
    }
}
