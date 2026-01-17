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

class PolicyMakerRoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $policyMaker;
    protected User $governmentalOfficial;
    protected User $otherPolicyMaker;
    protected User $superAdmin;

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
        // Create Policy Maker (main test user)
        $this->policyMaker = User::create([
            'first_name' => 'Policy',
            'last_name' => 'Maker',
            'email' => 'policy@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567890',
            'type' => UserTypeEnum::POLICY_MAKER,
            'active' => true,
        ]);

        // Create another Policy Maker
        $this->otherPolicyMaker = User::create([
            'first_name' => 'Other',
            'last_name' => 'PolicyMaker',
            'email' => 'otherpolicy@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567891',
            'type' => UserTypeEnum::POLICY_MAKER,
            'active' => true,
        ]);

        // Create Governmental Official for comparison
        $this->governmentalOfficial = User::create([
            'first_name' => 'Government',
            'last_name' => 'Official',
            'email' => 'official@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567892',
            'type' => UserTypeEnum::USER,
            'active' => true,
        ]);

        // Create Super Admin for comparison
        $this->superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567893',
            'type' => UserTypeEnum::ADMIN,
            'active' => true,
        ]);
    }

    /** @test */
    public function policy_maker_gets_assigned_correct_role_on_creation()
    {
        $this->assertTrue($this->policyMaker->hasRole('Policy Maker'));
        $this->assertFalse($this->policyMaker->hasRole('Super Admin'));
        $this->assertFalse($this->policyMaker->hasRole('Governmental Official'));
    }

    /** @test */
    public function policy_maker_has_very_limited_permissions()
    {
        // Permissions they SHOULD have (very limited set)
        $allowedPermissions = [
            'show analyst',
            'show rating',
            'create rating',
            'update rating',
            'delete rating',
            'show comment',
            'create comment',
            'update comment',
            'delete comment',
            'show post',
        ];

        foreach ($allowedPermissions as $permission) {
            $this->assertTrue(
                $this->policyMaker->hasPermissionTo($permission),
                "Policy Maker should have '{$permission}' permission"
            );
        }

        // Permissions they should NOT have (most permissions are denied)
        $deniedPermissions = [
            // User management
            'show user',
            'create user',
            'update user',
            'delete user',
            
            // Organization management
            'show organization',
            'create organization',
            'update organization',
            'delete organization',
            
            // Analyst management (can only view)
            'create analyst',
            'update analyst',
            'delete analyst',
            
            // Hashtag management (no permissions)
            'show hashtag',
            'create hashtag',
            'update hashtag',
            'delete hashtag',
            
            // Invite management (no permissions)
            'show invite',
            'create invite',
            'update invite',
            'delete invite',
            
            // Post management (can only view)
            'create post',
            'update post',
            'delete post',
        ];

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse(
                $this->policyMaker->hasPermissionTo($permission),
                "Policy Maker should NOT have '{$permission}' permission"
            );
        }
    }

    /** @test */
    public function policy_maker_cannot_access_user_profiles()
    {
        $this->actingAs($this->policyMaker);
        
        // Cannot view any user profiles (no 'show user' permission)
        $this->assertFalse($this->policyMaker->can('view', $this->policyMaker));
        $this->assertFalse($this->policyMaker->can('view', $this->otherPolicyMaker));
        $this->assertFalse($this->policyMaker->can('view', $this->governmentalOfficial));
        $this->assertFalse($this->policyMaker->can('view', $this->superAdmin));
        
        // Cannot update any user profiles
        $this->assertFalse($this->policyMaker->can('update', $this->policyMaker));
        $this->assertFalse($this->policyMaker->can('update', $this->otherPolicyMaker));
        $this->assertFalse($this->policyMaker->can('update', $this->governmentalOfficial));
        $this->assertFalse($this->policyMaker->can('update', $this->superAdmin));
    }

    /** @test */
    public function policy_maker_can_manage_own_ratings()
    {
        $this->actingAs($this->policyMaker);
        
        // Create own rating
        $ownRating = Rating::create([
            'user_id' => $this->policyMaker->id,
            'rating' => 5,
            'comment' => 'Policy maker rating'
        ]);
        
        // Can view ratings
        $this->assertTrue($this->policyMaker->can('viewAny', Rating::class));
        $this->assertTrue($this->policyMaker->can('view', $ownRating));
        
        // Can create ratings
        $this->assertTrue($this->policyMaker->can('create', Rating::class));
        
        // Can update own rating
        $this->assertTrue($this->policyMaker->can('update', $ownRating));
        
        // Can delete own rating
        $this->assertTrue($this->policyMaker->can('delete', $ownRating));
    }

    /** @test */
    public function policy_maker_cannot_manage_others_ratings()
    {
        $this->actingAs($this->policyMaker);
        
        // Create rating by another user
        $otherRating = Rating::create([
            'user_id' => $this->governmentalOfficial->id,
            'rating' => 4,
            'comment' => 'Official rating'
        ]);
        
        // Can view other ratings (show permission allows this)
        $this->assertTrue($this->policyMaker->can('view', $otherRating));
        
        // Cannot update other user's rating (ownership restriction)
        $this->assertFalse($this->policyMaker->can('update', $otherRating));
        
        // Cannot delete other user's rating (ownership restriction)
        $this->assertFalse($this->policyMaker->can('delete', $otherRating));
    }

    /** @test */
    public function policy_maker_has_no_invite_permissions()
    {
        $this->actingAs($this->policyMaker);
        
        // Create invite by another user
        $invite = Invite::create([
            'user_id' => $this->governmentalOfficial->id,
            'token' => 'test-token',
            'email' => 'test@test.com',
            'status' => 'pending',
            'expired_at' => now()->addDays(7),
            'expired' => 0
        ]);
        
        // Cannot view invites (no 'show invite' permission)
        $this->assertFalse($this->policyMaker->can('view', $invite));
        
        // Cannot create invites
        $this->assertFalse($this->policyMaker->can('create', Invite::class));
        
        // Cannot update invites
        $this->assertFalse($this->policyMaker->can('update', $invite));
        
        // Cannot delete invites
        $this->assertFalse($this->policyMaker->can('delete', $invite));
    }

    /** @test */
    public function policy_maker_has_no_hashtag_permissions()
    {
        $this->actingAs($this->policyMaker);
        
        // No hashtag permissions at all
        $this->assertFalse($this->policyMaker->hasPermissionTo('show hashtag'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('create hashtag'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('update hashtag'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('delete hashtag'));
    }

    /** @test */
    public function policy_maker_has_read_only_access_to_posts()
    {
        $this->actingAs($this->policyMaker);
        
        // Can view posts
        $this->assertTrue($this->policyMaker->hasPermissionTo('show post'));
        
        // Cannot create posts
        $this->assertFalse($this->policyMaker->hasPermissionTo('create post'));
        
        // Cannot update posts
        $this->assertFalse($this->policyMaker->hasPermissionTo('update post'));
        
        // Cannot delete posts
        $this->assertFalse($this->policyMaker->hasPermissionTo('delete post'));
    }

    /** @test */
    public function policy_maker_has_read_only_access_to_analysts()
    {
        $this->actingAs($this->policyMaker);
        
        // Can view analysts
        $this->assertTrue($this->policyMaker->hasPermissionTo('show analyst'));
        
        // Cannot manage analysts
        $this->assertFalse($this->policyMaker->hasPermissionTo('create analyst'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('update analyst'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('delete analyst'));
    }

    /** @test */
    public function policy_maker_has_no_organization_access()
    {
        $this->actingAs($this->policyMaker);
        
        // No organization permissions at all
        $this->assertFalse($this->policyMaker->hasPermissionTo('show organization'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('create organization'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('update organization'));
        $this->assertFalse($this->policyMaker->hasPermissionTo('delete organization'));
    }

    /** @test */
    public function policy_maker_can_manage_comments()
    {
        $this->actingAs($this->policyMaker);
        
        // Has full CRUD permissions for comments
        $this->assertTrue($this->policyMaker->hasPermissionTo('show comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('create comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('update comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('delete comment'));
    }

    /** @test */
    public function policy_maker_role_assignment_changes_with_type_update()
    {
        // Initially Policy Maker
        $this->assertTrue($this->policyMaker->hasRole('Policy Maker'));
        
        // Change type to Governmental Official
        $this->policyMaker->update(['type' => UserTypeEnum::USER]);
        $this->policyMaker->refresh();
        
        $this->assertFalse($this->policyMaker->hasRole('Policy Maker'));
        $this->assertTrue($this->policyMaker->hasRole('Governmental Official'));
        
        // Change back to Policy Maker
        $this->policyMaker->update(['type' => UserTypeEnum::POLICY_MAKER]);
        $this->policyMaker->refresh();
        
        $this->assertTrue($this->policyMaker->hasRole('Policy Maker'));
        $this->assertFalse($this->policyMaker->hasRole('Governmental Official'));
    }

    /** @test */
    public function policy_maker_can_be_deleted()
    {
        // Unlike Super Admin, Policy Makers can be deleted
        $userId = $this->policyMaker->id;
        
        $this->policyMaker->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    /** @test */
    public function policy_maker_has_most_restricted_permissions()
    {
        $this->actingAs($this->policyMaker);
        
        // Get all permissions
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $policyMakerPermissions = $this->policyMaker->getAllPermissions()->pluck('name')->toArray();
        $governmentalOfficialPermissions = $this->governmentalOfficial->getAllPermissions()->pluck('name')->toArray();
        
        // Policy Maker should have fewer permissions than Governmental Official
        $this->assertLessThan(count($governmentalOfficialPermissions), count($policyMakerPermissions));
        
        // Policy Maker should have much fewer permissions than total available
        $this->assertLessThan(count($allPermissions) / 2, count($policyMakerPermissions));
        
        // Specifically should not have user management permissions
        $this->assertNotContains('show user', $policyMakerPermissions);
        $this->assertNotContains('create user', $policyMakerPermissions);
        $this->assertNotContains('update user', $policyMakerPermissions);
        $this->assertNotContains('delete user', $policyMakerPermissions);
        
        // Should not have invite permissions
        $this->assertNotContains('show invite', $policyMakerPermissions);
        $this->assertNotContains('create invite', $policyMakerPermissions);
        $this->assertNotContains('update invite', $policyMakerPermissions);
        $this->assertNotContains('delete invite', $policyMakerPermissions);
        
        // Should not have hashtag permissions
        $this->assertNotContains('show hashtag', $policyMakerPermissions);
        $this->assertNotContains('create hashtag', $policyMakerPermissions);
        $this->assertNotContains('update hashtag', $policyMakerPermissions);
        $this->assertNotContains('delete hashtag', $policyMakerPermissions);
        
        // Should not have organization permissions
        $this->assertNotContains('show organization', $policyMakerPermissions);
        $this->assertNotContains('create organization', $policyMakerPermissions);
        $this->assertNotContains('update organization', $policyMakerPermissions);
        $this->assertNotContains('delete organization', $policyMakerPermissions);
    }

    /** @test */
    public function policy_maker_permissions_comparison_with_other_roles()
    {
        // Policy Maker permissions (most restricted)
        $policyMakerPermissions = $this->policyMaker->getAllPermissions()->pluck('name')->toArray();
        
        // Governmental Official permissions (medium level)
        $governmentalOfficialPermissions = $this->governmentalOfficial->getAllPermissions()->pluck('name')->toArray();
        
        // Super Admin permissions (unrestricted)
        $superAdminPermissions = Permission::all()->pluck('name')->toArray();
        
        // Verify hierarchy: Policy Maker < Governmental Official < Super Admin
        $this->assertLessThan(
            count($governmentalOfficialPermissions), 
            count($policyMakerPermissions),
            'Policy Maker should have fewer permissions than Governmental Official'
        );
        
        $this->assertLessThan(
            count($superAdminPermissions), 
            count($governmentalOfficialPermissions),
            'Governmental Official should have fewer permissions than Super Admin'
        );
        
        // Policy Maker should only have permissions that are subset of Governmental Official
        $policyMakerOnlyPermissions = array_diff($policyMakerPermissions, $governmentalOfficialPermissions);
        $this->assertEmpty(
            $policyMakerOnlyPermissions,
            'Policy Maker should not have any permissions that Governmental Official does not have'
        );
    }

    /** @test */
    public function policy_maker_core_functionality_is_ratings_and_comments()
    {
        $this->actingAs($this->policyMaker);
        
        // Core functionality: Ratings
        $this->assertTrue($this->policyMaker->hasPermissionTo('show rating'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('create rating'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('update rating'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('delete rating'));
        
        // Core functionality: Comments
        $this->assertTrue($this->policyMaker->hasPermissionTo('show comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('create comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('update comment'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('delete comment'));
        
        // Read-only access: Posts and Analysts
        $this->assertTrue($this->policyMaker->hasPermissionTo('show post'));
        $this->assertTrue($this->policyMaker->hasPermissionTo('show analyst'));
        
        // Everything else is restricted
        $restrictedAreas = [
            'user', 'organization', 'hashtag', 'invite'
        ];
        
        foreach ($restrictedAreas as $area) {
            $this->assertFalse($this->policyMaker->hasPermissionTo("show {$area}"));
            $this->assertFalse($this->policyMaker->hasPermissionTo("create {$area}"));
            $this->assertFalse($this->policyMaker->hasPermissionTo("update {$area}"));
            $this->assertFalse($this->policyMaker->hasPermissionTo("delete {$area}"));
        }
    }
}