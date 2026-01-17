<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Models\Rating;
use App\Models\Invite;
use App\Models\Hashtag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class GovernmentalOfficialRoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $governmentalOfficial;
    protected User $otherUser;
    protected User $superAdmin;
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
        // Create Governmental Official (main test user)
        $this->governmentalOfficial = User::create([
            'first_name' => 'Government',
            'last_name' => 'Official',
            'email' => 'official@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567890',
            'type' => UserTypeEnum::USER,
            'active' => true,
        ]);

        // Create another regular user
        $this->otherUser = User::create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567891',
            'type' => UserTypeEnum::USER,
            'active' => true,
        ]);

        // Create Super Admin for comparison
        $this->superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567892',
            'type' => UserTypeEnum::ADMIN,
            'active' => true,
        ]);

        // Create Policy Maker
        $this->policyMaker = User::create([
            'first_name' => 'Policy',
            'last_name' => 'Maker',
            'email' => 'policy@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567893',
            'type' => UserTypeEnum::POLICY_MAKER,
            'active' => true,
        ]);
    }

    /** @test */
    public function governmental_official_gets_assigned_correct_role_on_creation()
    {
        $this->assertTrue($this->governmentalOfficial->hasRole('Governmental Official'));
        $this->assertFalse($this->governmentalOfficial->hasRole('Super Admin'));
        $this->assertFalse($this->governmentalOfficial->hasRole('Policy Maker'));
    }

    /** @test */
    public function governmental_official_has_correct_permissions()
    {
        // Permissions they SHOULD have
        $allowedPermissions = [
            'show user',
            'update user',
            'show organization',
            'show analyst',
            'show rating',
            'create rating',
            'update rating',
            'delete rating',
            'show comment',
            'create comment',
            'update comment',
            'delete comment',
            'show hashtag',
            'create hashtag',
            'delete hashtag',
            'show invite',
            'create invite',
            'update invite',
            'delete invite',
            'show post',
        ];

        foreach ($allowedPermissions as $permission) {
            $this->assertTrue(
                $this->governmentalOfficial->hasPermissionTo($permission),
                "Governmental Official should have '{$permission}' permission"
            );
        }

        // Permissions they should NOT have
        $deniedPermissions = [
            'create user',
            'delete user',
            'create organization',
            'update organization',
            'delete organization',
            'create analyst',
            'update analyst',
            'delete analyst',
            'update hashtag', // They can create and delete but not update
            'create post',
            'update post',
            'delete post',
        ];

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse(
                $this->governmentalOfficial->hasPermissionTo($permission),
                "Governmental Official should NOT have '{$permission}' permission"
            );
        }
    }

    /** @test */
    public function governmental_official_can_view_and_update_own_profile()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Can view own profile
        $this->assertTrue($this->governmentalOfficial->can('view', $this->governmentalOfficial));
        
        // Can update own profile
        $this->assertTrue($this->governmentalOfficial->can('update', $this->governmentalOfficial));
    }

    /** @test */
    public function governmental_official_cannot_view_other_users_profiles()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Cannot view other users' profiles (only Super Admin can)
        $this->assertFalse($this->governmentalOfficial->can('view', $this->otherUser));
        $this->assertFalse($this->governmentalOfficial->can('view', $this->policyMaker));
        $this->assertFalse($this->governmentalOfficial->can('view', $this->superAdmin));
    }

    /** @test */
    public function governmental_official_cannot_update_other_users_profiles()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Cannot update other users' profiles
        $this->assertFalse($this->governmentalOfficial->can('update', $this->otherUser));
        $this->assertFalse($this->governmentalOfficial->can('update', $this->policyMaker));
        $this->assertFalse($this->governmentalOfficial->can('update', $this->superAdmin));
    }

    /** @test */
    public function governmental_official_can_manage_own_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Create own rating
        $ownRating = Rating::create([
            'user_id' => $this->governmentalOfficial->id,
            'rating' => 5,
            'comment' => 'My own rating'
        ]);
        
        // Can view ratings
        $this->assertTrue($this->governmentalOfficial->can('viewAny', Rating::class));
        $this->assertTrue($this->governmentalOfficial->can('view', $ownRating));
        
        // Can create ratings
        $this->assertTrue($this->governmentalOfficial->can('create', Rating::class));
        
        // Can update own rating
        $this->assertTrue($this->governmentalOfficial->can('update', $ownRating));
        
        // Can delete own rating
        $this->assertTrue($this->governmentalOfficial->can('delete', $ownRating));
    }

    /** @test */
    public function governmental_official_cannot_manage_others_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Create rating by another user
        $otherRating = Rating::create([
            'user_id' => $this->otherUser->id,
            'rating' => 4,
            'comment' => 'Other user rating'
        ]);
        
        // Can view other ratings (show permission allows this)
        $this->assertTrue($this->governmentalOfficial->can('view', $otherRating));
        
        // Cannot update other user's rating (ownership restriction)
        $this->assertFalse($this->governmentalOfficial->can('update', $otherRating));
        
        // Cannot delete other user's rating (ownership restriction)
        $this->assertFalse($this->governmentalOfficial->can('delete', $otherRating));
    }

    /** @test */
    public function governmental_official_can_manage_own_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Create own invite
        $ownInvite = Invite::create([
            'user_id' => $this->governmentalOfficial->id,
            'token' => 'my-token',
            'email' => 'myinvite@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        // Can view own invite
        $this->assertTrue($this->governmentalOfficial->can('view', $ownInvite));
        
        // Can create invites
        $this->assertTrue($this->governmentalOfficial->can('create', Invite::class));
        
        // Can update own invite
        $this->assertTrue($this->governmentalOfficial->can('update', $ownInvite));
        
        // Can delete own invite
        $this->assertTrue($this->governmentalOfficial->can('delete', $ownInvite));
    }

    /** @test */
    public function governmental_official_cannot_manage_others_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Create invite by another user
        $otherInvite = Invite::create([
            'user_id' => $this->otherUser->id,
            'token' => 'other-token',
            'email' => 'otherinvite@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        // Cannot view other user's invite (ownership restriction)
        $this->assertFalse($this->governmentalOfficial->can('view', $otherInvite));
        
        // Cannot update other user's invite
        $this->assertFalse($this->governmentalOfficial->can('update', $otherInvite));
        
        // Cannot delete other user's invite
        $this->assertFalse($this->governmentalOfficial->can('delete', $otherInvite));
    }

    /** @test */
    public function governmental_official_can_manage_hashtags()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Can view hashtags
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('show hashtag'));
        
        // Can create hashtags
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('create hashtag'));
        
        // Can delete hashtags
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('delete hashtag'));
        
        // Cannot update hashtags (not in their permission list)
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('update hashtag'));
    }

    /** @test */
    public function governmental_official_has_read_only_access_to_posts()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Can view posts
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('show post'));
        
        // Cannot create posts
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('create post'));
        
        // Cannot update posts
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('update post'));
        
        // Cannot delete posts
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('delete post'));
    }

    /** @test */
    public function governmental_official_has_read_only_access_to_organizations_and_analysts()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Can view organizations
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('show organization'));
        
        // Cannot manage organizations
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('create organization'));
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('update organization'));
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('delete organization'));
        
        // Can view analysts
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('show analyst'));
        
        // Cannot manage analysts
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('create analyst'));
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('update analyst'));
        $this->assertFalse($this->governmentalOfficial->hasPermissionTo('delete analyst'));
    }

    /** @test */
    public function governmental_official_can_manage_comments()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Has full CRUD permissions for comments
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('show comment'));
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('create comment'));
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('update comment'));
        $this->assertTrue($this->governmentalOfficial->hasPermissionTo('delete comment'));
    }

    /** @test */
    public function governmental_official_role_assignment_changes_with_type_update()
    {
        // Initially Governmental Official
        $this->assertTrue($this->governmentalOfficial->hasRole('Governmental Official'));
        
        // Change type to Policy Maker
        $this->governmentalOfficial->update(['type' => UserTypeEnum::POLICY_MAKER]);
        $this->governmentalOfficial->refresh();
        
        $this->assertFalse($this->governmentalOfficial->hasRole('Governmental Official'));
        $this->assertTrue($this->governmentalOfficial->hasRole('Policy Maker'));
        
        // Change back to User
        $this->governmentalOfficial->update(['type' => UserTypeEnum::USER]);
        $this->governmentalOfficial->refresh();
        
        $this->assertTrue($this->governmentalOfficial->hasRole('Governmental Official'));
        $this->assertFalse($this->governmentalOfficial->hasRole('Policy Maker'));
    }

    /** @test */
    public function governmental_official_can_be_deleted()
    {
        // Unlike Super Admin, regular users can be deleted
        $userId = $this->governmentalOfficial->id;
        
        $this->governmentalOfficial->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    /** @test */
    public function governmental_official_permissions_are_limited_compared_to_super_admin()
    {
        $this->actingAs($this->governmentalOfficial);
        
        // Get all permissions
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $userPermissions = $this->governmentalOfficial->getAllPermissions()->pluck('name')->toArray();
        
        // User should have fewer permissions than total available
        $this->assertLessThan(count($allPermissions), count($userPermissions));
        
        // Specifically should not have user management permissions
        $this->assertNotContains('create user', $userPermissions);
        $this->assertNotContains('delete user', $userPermissions);
        
        // Should not have post management permissions
        $this->assertNotContains('create post', $userPermissions);
        $this->assertNotContains('update post', $userPermissions);
        $this->assertNotContains('delete post', $userPermissions);
    }
}