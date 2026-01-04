<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RolesAndPermissionsSeeder;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed initial roles/permissions
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_create_manager_with_role_and_permissions()
    {
        $superAdmin = User::where('email', 'admin@skaikcenter.com')->first();

        $response = $this->actingAs($superAdmin)->postJson('/api/managers', [
            'name' => 'New Center Manger',
            'email' => 'manager@example.com',
            'password' => 'password123',
            'type' => 'center_admin',
            'roles' => ['sub_admin'], // Assign sub_admin role
            'permissions' => ['manage_finance'] // Direct permission override
        ]);

        $response->assertStatus(201);

        $manager = User::where('email', 'manager@example.com')->first();

        // Assert Role
        $this->assertTrue($manager->hasRole('sub_admin'));

        // Assert Direct Permission (Hybrid system check)
        $this->assertTrue($manager->hasDirectPermission('manage_finance'));
    }

    public function test_super_admin_can_modify_manager_permissions()
    {
        $superAdmin = User::where('email', 'admin@skaikcenter.com')->first();

        // Create a manager first
        $manager = User::create([
            'name' => 'Existing Manager',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'type' => 'center_admin'
        ]);
        $manager->assignRole('sub_admin'); // Has 'manage_users' by default from seeder

        // Update: Revoke Role, Give specific permission
        $response = $this->actingAs($superAdmin)->putJson("/api/managers/{$manager->id}", [
            'roles' => [], // Remove roles
            'permissions' => ['view_reports']
        ]);

        $response->assertStatus(200);

        $manager->refresh();
        $this->assertFalse($manager->hasRole('sub_admin'));
        $this->assertTrue($manager->hasPermissionTo('view_reports'));
    }
}
