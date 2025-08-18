<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_impersonate_admin()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Start impersonation
        $response = $this->get(route('impersonate.start', $admin->id));

        // Should redirect to dashboard
        $response->assertRedirect(route('dashboard'));

        // Should be logged in as admin
        $this->assertAuthenticatedAs($admin);

        // Should have impersonation session data
        $this->assertSessionHas('impersonator_id', $superadmin->id);
        $this->assertSessionHas('impersonator_name', $superadmin->name);
        $this->assertSessionHas('impersonator_email', $superadmin->email);
    }

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

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
            'hotel_id' => $hotel->id,
        ]);

        // Create another admin user
        $targetAdmin = User::factory()->create([
            'role' => 'admin',
            'hotel_id' => $hotel->id,
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Try to impersonate another admin
        $response = $this->get(route('impersonate.start', $targetAdmin->id));

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

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Start impersonation
        $this->get(route('impersonate.start', $admin->id));

        // Should be logged in as admin
        $this->assertAuthenticatedAs($admin);

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

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
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
        $this->get(route('impersonate.start', $admin->id));

        // Check status during impersonation
        $response = $this->getJson(route('impersonate.status'));
        $response->assertJson([
            'is_impersonating' => true,
            'impersonator_name' => $superadmin->name,
            'impersonator_email' => $superadmin->email,
        ]);
    }

    public function test_superadmin_can_impersonate_both_admin_and_manager()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
            'hotel_id' => $hotel->id,
        ]);

        // Create a manager user
        $manager = User::factory()->create([
            'role' => 'manager',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Test impersonating admin
        $response = $this->get(route('impersonate.start', $admin->id));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        // Stop impersonation
        $this->get(route('impersonate.stop'));
        $this->assertAuthenticatedAs($superadmin);

        // Test impersonating manager
        $response = $this->get(route('impersonate.start', $manager->id));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    public function test_superadmin_cannot_impersonate_other_roles()
    {
        // Create a hotel
        $hotel = Hotel::factory()->create();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'hotel_id' => null,
        ]);

        // Create a cashier user
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'hotel_id' => $hotel->id,
        ]);

        // Create a frontdesk user
        $frontdesk = User::factory()->create([
            'role' => 'frontdesk',
            'hotel_id' => $hotel->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Try to impersonate cashier
        $response = $this->get(route('impersonate.start', $cashier->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Try to impersonate frontdesk
        $response = $this->get(route('impersonate.start', $frontdesk->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
