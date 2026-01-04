<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

class ArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_soft_delete_logic()
    {
        $admin = User::first();
        $this->actingAs($admin);

        $user = User::create([
            'name' => 'Target User',
            'email' => 'target@example.com',
            'password' => 'password',
            'status' => 'active',
            'type' => 'student'
        ]);

        $this->assertEquals($admin->id, $user->created_by); // Should be set if logged in. 
        // Trait logic: static::creating(function ($model) { if (Auth::check()) ...
        // actingAs sets the user for the application, so Auth::check() should be true.

        // Re-fetch to check DB values
        $user->refresh();
        $this->assertEquals($admin->id, $user->created_by);

        // Update
        $user->name = 'Updated Name';
        $user->save();
        $this->assertEquals($admin->id, $user->updated_by);

        // Soft Delete
        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNotNull($user->deleted_by);
        $this->assertEquals($admin->id, $user->deleted_by);
        $this->assertTrue($user->is_deleted); // We manually added this column 
        // Wait, standard soft delete trait doesn't toggle `is_deleted` boolean automatically unless we override delete() or use an observer.
        // My Auditable trait used `static::deleting`.
        // Let's verify if I implemented `is_deleted = true` in the trait.
    }
}
