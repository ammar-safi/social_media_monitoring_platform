<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Models\Rating;
use App\Models\Invite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminRoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $superAdmin;
    protected User $regularUser;
    protected User $policyMaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders to set up roles and permissions
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'MainRoles']);
        
        // Create test users
        $this->createTestUsers();
    }

    private function createTestUsers(): void
    {
        // Create Super Admin
        $this->superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567890',
            'type' => UserTypeEnum::ADMIN,
            'active' => true,
        ]);

        // Create Regular User (Governmental Official)
        $this->regularUser = User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567891',
            'type' => UserTypeEnum::USER,
            'active' => true,
        ]);

        // Create Policy Maker
        $this->policyMaker = User::create([
            'first_name' => 'Policy',
            'last_name' => 'Maker',
            'email' => 'policy@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567892',
            'type' => UserTypeEnum::POLICY_MAKER,
            'active' => true,
        ]);
    }

    /** @test */
    public function super_admin_gets_assigned_correct_role_on_creation()
    {
        $this->assertTrue($this->superAdmin->hasRole('Super Admin'));
        $this->assertFalse($this->superAdmin->hasRole('Governmental Official'));
        $this->assertFalse($this->superAdmin->hasRole('Policy Maker'));
    }

    /** @test */
    public function super_admin_has_all_permissions()
    {
        $allPermissions = Permission::all()->pluck('name')->toArray();
        
        foreach ($allPermissions as $permission) {
            $this->assertTrue(
                $this->superAdmin->can($permission),
                "Super Admin should have '{$permission}' permission"
            );
        }
    }

    /** @test */
    public function super_admin_can_view_any_user_profile()
    {
        $this->actingAs($this->superAdmin);
        
        // Super Admin can view other users' profiles
        $this->assertTrue($this->superAdmin->can('view', $this->regularUser));
        $this->assertTrue($this->superAdmin->can('view', $this->policyMaker));
        
        // Super Admin can view their own profile
        $this->assertTrue($this->superAdmin->can('view', $this->superAdmin));
    }

    /** @test */
    public function super_admin_can_update_any_user_profile()
    {
        $this->actingAs($this->superAdmin);
        
        // Super Admin can update other users' profiles
        $this->assertTrue($this->superAdmin->can('update', $this->regularUser));
        $this->assertTrue($this->superAdmin->can('update', $this->policyMaker));
        
        // Super Admin can update their own profile
        $this->assertTrue($this->superAdmin->can('update', $this->superAdmin));
    }

    /** @test */
    public function super_admin_can_manage_all_ratings()
    {
        $this->actingAs($this->superAdmin);
        
        // Create ratings by different users
        $userRating = Rating::create([
            'user_id' => $this->regularUser->id,
            'rating' => 5,
            'comment' => 'Test rating'
        ]);
        
        $policyMakerRating = Rating::create([
            'user_id' => $this->policyMaker->id,
            'rating' => 4,
            'comment' => 'Policy maker rating'
        ]);
        
        $adminRating = Rating::create([
            'user_id' => $this->superAdmin->id,
            'rating' => 3,
            'comment' => 'Admin rating'
        ]);
        
        // Super Admin can view all ratings
        $this->assertTrue($this->superAdmin->can('viewAny', Rating::class));
        $this->assertTrue($this->superAdmin->can('view', $userRating));
        $this->assertTrue($this->superAdmin->can('view', $policyMakerRating));
        $this->assertTrue($this->superAdmin->can('view', $adminRating));
        
        // Super Admin can create ratings
        $this->assertTrue($this->superAdmin->can('create', Rating::class));
        
        // Super Admin can update any rating
        $this->assertTrue($this->superAdmin->can('update', $userRating));
        $this->assertTrue($this->superAdmin->can('update', $policyMakerRating));
        $this->assertTrue($this->superAdmin->can('update', $adminRating));
        
        // Super Admin can delete any rating
        $this->assertTrue($this->superAdmin->can('delete', $userRating));
        $this->assertTrue($this->superAdmin->can('delete', $policyMakerRating));
        $this->assertTrue($this->superAdmin->can('delete', $adminRating));
    }

    /** @test */
    public function super_admin_can_manage_all_invites()
    {
        $this->actingAs($this->superAdmin);
        
        // Create invites by different users
        $userInvite = Invite::create([
            'user_id' => $this->regularUser->id,
            'token' => 'test-token-1',
            'email' => 'invite1@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        $adminInvite = Invite::create([
            'user_id' => $this->superAdmin->id,
            'token' => 'test-token-2',
            'email' => 'invite2@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        // Super Admin can view all invites
        $this->assertTrue($this->superAdmin->can('view', $userInvite));
        $this->assertTrue($this->superAdmin->can('view', $adminInvite));
        
        // Super Admin can create invites
        $this->assertTrue($this->superAdmin->can('create', Invite::class));
        
        // Super Admin can update any invite
        $this->assertTrue($this->superAdmin->can('update', $userInvite));
        $this->assertTrue($this->superAdmin->can('update', $adminInvite));
        
        // Super Admin can delete any invite
        $this->assertTrue($this->superAdmin->can('delete', $userInvite));
        $this->assertTrue($this->superAdmin->can('delete', $adminInvite));
    }

    /** @test */
    // public function super_admin_has_all_resource_permissions()
    // {
    //     $this->actingAs($this->superAdmin);
        
    //     $resources = ['user', 'organization', 'analyst', 'rating', 'comment', 'hashtag', 'invite', 'post'];
    //     $actions = ['create', 'update', 'show', 'delete'];
        
    //     foreach ($resources as $resource) {
    //         foreach ($actions as $action) {
    //             $permission = "{$action} {$resource}";
    //             $this->assertTrue(
    //                 $this->superAdmin->hasPermissionTo($permission),
    //                 "Super Admin should have '{$permission}' permission"
    //             );
    //         }
    //     }
    // }

    /** @test */
    public function super_admin_cannot_be_deleted()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('you cannot delete this admin');
        
        $this->superAdmin->delete();
    }

    /** @test */
    public function super_admin_role_assignment_changes_with_type_update()
    {
        // Initially admin
        $this->assertTrue($this->superAdmin->hasRole('Super Admin'));
        
        // Change type to user
        $this->superAdmin->update(['type' => UserTypeEnum::USER]);
        $this->superAdmin->refresh();
        
        $this->assertFalse($this->superAdmin->hasRole('Super Admin'));
        $this->assertTrue($this->superAdmin->hasRole('Governmental Official'));
        
        // Change back to admin
        $this->superAdmin->update(['type' => UserTypeEnum::ADMIN]);
        $this->superAdmin->refresh();
        
        $this->assertTrue($this->superAdmin->hasRole('Super Admin'));
        $this->assertFalse($this->superAdmin->hasRole('Governmental Official'));
    }

    /** @test */
    // public function super_admin_can_access_all_models()
    // {
    //     $this->actingAs($this->superAdmin);
        
    //     // Test with different model types that might exist
    //     $modelPermissions = [
    //         'show user',
    //         'show organization', 
    //         'show analyst',
    //         'show rating',
    //         'show comment',
    //         'show hashtag',
    //         'show invite',
    //         'show post'
    //     ];
        
    //     foreach ($modelPermissions as $permission) {
    //         $this->assertTrue(
    //             $this->superAdmin->hasPermissionTo($permission),
    //             "Super Admin should have '{$permission}' permission"
    //         );
    //     }
    // }

    /** @test */
    public function super_admin_permissions_override_ownership_restrictions()
    {
        $this->actingAs($this->superAdmin);
        
        // Create a rating owned by another user
        $otherUserRating = Rating::create([
            'user_id' => $this->regularUser->id,
            'rating' => 4,
            'comment' => 'Other user rating'
        ]);
        
        // Super Admin should be able to update/delete it even though they don't own it
        $this->assertTrue($this->superAdmin->can('update', $otherUserRating));
        $this->assertTrue($this->superAdmin->can('delete', $otherUserRating));
        
        // Create an invite owned by another user
        $otherUserInvite = Invite::create([
            'user_id' => $this->regularUser->id,
            'token' => 'other-user-token',
            'email' => 'other@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        // Super Admin should be able to manage it
        $this->assertTrue($this->superAdmin->can('view', $otherUserInvite));
        $this->assertTrue($this->superAdmin->can('update', $otherUserInvite));
        $this->assertTrue($this->superAdmin->can('delete', $otherUserInvite));
    }

    /** @test */
    // public function super_admin_has_create_permissions_for_all_resources()
    // {
    //     $this->actingAs($this->superAdmin);
        
    //     $createPermissions = [
    //         'create user',
    //         'create organization',
    //         'create analyst', 
    //         'create rating',
    //         'create comment',
    //         'create hashtag',
    //         'create invite',
    //         'create post'
    //     ];
        
    //     foreach ($createPermissions as $permission) {
    //         $this->assertTrue(
    //             $this->superAdmin->hasPermissionTo($permission),
    //             "Super Admin should have '{$permission}' permission"
    //         );
    //     }
    // }

    /** @test */
    // public function super_admin_maintains_permissions_after_role_sync()
    // {
    //     // Manually sync roles to ensure permissions are maintained
    //     $this->superAdmin->syncRoles(['Super Admin']);
        
    //     // Verify all permissions are still there
    //     $this->assertTrue($this->superAdmin->hasRole('Super Admin'));
    //     $this->assertTrue($this->superAdmin->hasPermissionTo('create user'));
    //     $this->assertTrue($this->superAdmin->hasPermissionTo('delete rating'));
    //     $this->assertTrue($this->superAdmin->hasPermissionTo('update invite'));
    // }
}