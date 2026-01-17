<?php

namespace Tests\Feature\Policies;

use App\Models\ApproveUser;
use App\Policies\ApproveUserPolicy;

class ApproveUserPolicyTest extends BasePolicyTest
{
    private ApproveUserPolicy $policy;
    private ApproveUser $testApproveUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ApproveUserPolicy();
        $this->testApproveUser = ApproveUser::factory()->create();
    }

    /** @test */
    public function super_admin_cannot_perform_any_approve_user_actions()
    {
        // Even Super Admin cannot access ApproveUser in your implementation
        $this->assertFalse($this->policy->viewAny($this->superAdmin));
        $this->assertFalse($this->policy->view($this->superAdmin, $this->testApproveUser));
        $this->assertFalse($this->policy->create($this->superAdmin));
        $this->assertFalse($this->policy->update($this->superAdmin, $this->testApproveUser));
        $this->assertFalse($this->policy->delete($this->superAdmin, $this->testApproveUser));
    }

    /** @test */
    public function super_admin_can_reorder_approve_users()
    {
        // Only reorder() returns true in your implementation
        $this->assertTrue($this->policy->reorder($this->superAdmin));
    }

    /** @test */
    public function governmental_official_cannot_perform_any_approve_user_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->view($this->governmentalOfficial, $this->testApproveUser));
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testApproveUser));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testApproveUser));
    }

    /** @test */
    public function governmental_official_can_reorder_approve_users()
    {
        $this->assertTrue($this->policy->reorder($this->governmentalOfficial));
    }

    /** @test */
    public function policy_maker_cannot_perform_any_approve_user_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->policyMaker));
        $this->assertFalse($this->policy->view($this->policyMaker, $this->testApproveUser));
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testApproveUser));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testApproveUser));
    }

    /** @test */
    public function policy_maker_can_reorder_approve_users()
    {
        $this->assertTrue($this->policy->reorder($this->policyMaker));
    }

    /** @test */
    public function regular_user_cannot_perform_any_approve_user_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testApproveUser));
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testApproveUser));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testApproveUser));
    }

    /** @test */
    public function regular_user_can_reorder_approve_users()
    {
        $this->assertTrue($this->policy->reorder($this->regularUser));
    }

    /** @test */
    public function no_one_can_delete_any_approve_users()
    {
        $this->assertFalse($this->policy->deleteAny($this->superAdmin));
        $this->assertFalse($this->policy->deleteAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->deleteAny($this->policyMaker));
        $this->assertFalse($this->policy->deleteAny($this->regularUser));
    }
}