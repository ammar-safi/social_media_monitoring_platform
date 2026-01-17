<?php

namespace Tests\Feature\Policies;

use App\Models\Invite;
use App\Policies\InvitePolicy;

class InvitePolicyTest extends BasePolicyTest
{
    private InvitePolicy $policy;
    private Invite $testInvite;
    private Invite $userOwnedInvite;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new InvitePolicy();
        
        // Create an invite owned by someone else
        $this->testInvite = Invite::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        // Create an invite owned by governmental official
        $this->userOwnedInvite = Invite::factory()->create([
            'user_id' => $this->governmentalOfficial->id
        ]);
    }

    /** @test */
    public function super_admin_cannot_view_any_invites()
    {
        // Even Super Admin cannot viewAny - returns false in your implementation
        $this->assertFalse($this->policy->viewAny($this->superAdmin));
    }

    /** @test */
    public function super_admin_can_view_specific_invites()
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue($this->policy->view($this->superAdmin, $this->testInvite));
        $this->assertTrue($this->policy->view($this->superAdmin, $this->userOwnedInvite));
    }

    /** @test */
    public function super_admin_can_create_invites()
    {
        $this->assertTrue($this->policy->create($this->superAdmin));
    }

    /** @test */
    public function super_admin_can_update_any_invite()
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue($this->policy->update($this->superAdmin, $this->testInvite));
        $this->assertTrue($this->policy->update($this->superAdmin, $this->userOwnedInvite));
    }

    /** @test */
    public function super_admin_can_delete_any_invite()
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue($this->policy->delete($this->superAdmin, $this->testInvite));
        $this->assertTrue($this->policy->delete($this->superAdmin, $this->userOwnedInvite));
    }

    /** @test */
    public function governmental_official_cannot_view_any_invites()
    {
        $this->assertFalse($this->policy->viewAny($this->governmentalOfficial));
    }

    /** @test */
    public function governmental_official_can_view_own_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertTrue($this->policy->view($this->governmentalOfficial, $this->userOwnedInvite));
    }

    /** @test */
    public function governmental_official_cannot_view_others_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertFalse($this->policy->view($this->governmentalOfficial, $this->testInvite));
    }

    /** @test */
    public function governmental_official_can_create_invites()
    {
        // Governmental Official has "create invite" permission
        $this->assertTrue($this->policy->create($this->governmentalOfficial));
    }

    /** @test */
    public function governmental_official_can_update_own_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertTrue($this->policy->update($this->governmentalOfficial, $this->userOwnedInvite));
    }

    /** @test */
    public function governmental_official_cannot_update_others_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testInvite));
    }

    /** @test */
    public function governmental_official_can_delete_own_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertTrue($this->policy->delete($this->governmentalOfficial, $this->userOwnedInvite));
    }

    /** @test */
    public function governmental_official_cannot_delete_others_invites()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testInvite));
    }

    /** @test */
    public function policy_maker_cannot_access_invites()
    {
        // Policy Maker does NOT have invite permissions
        $this->assertFalse($this->policy->viewAny($this->policyMaker));
        $this->assertFalse($this->policy->create($this->policyMaker));
        
        $this->actingAs($this->policyMaker);
        $this->assertFalse($this->policy->view($this->policyMaker, $this->testInvite));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testInvite));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testInvite));
    }

    /** @test */
    public function regular_user_cannot_access_invites()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->create($this->regularUser));
        
        $this->actingAs($this->regularUser);
        $this->assertFalse($this->policy->view($this->regularUser, $this->testInvite));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testInvite));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testInvite));
    }

    /** @test */
    public function all_users_can_reorder_invites()
    {
        $this->assertTrue($this->policy->reorder($this->superAdmin));
        $this->assertTrue($this->policy->reorder($this->governmentalOfficial));
        $this->assertTrue($this->policy->reorder($this->policyMaker));
        $this->assertTrue($this->policy->reorder($this->regularUser));
    }

    /** @test */
    public function no_one_can_delete_any_invites()
    {
        $this->assertFalse($this->policy->deleteAny($this->superAdmin));
        $this->assertFalse($this->policy->deleteAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->deleteAny($this->policyMaker));
        $this->assertFalse($this->policy->deleteAny($this->regularUser));
    }
}