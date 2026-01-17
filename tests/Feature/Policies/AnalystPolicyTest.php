<?php

namespace Tests\Feature\Policies;

use App\Models\Analyst;
use App\Policies\AnalystPolicy;

class AnalystPolicyTest extends BasePolicyTest
{
    private AnalystPolicy $policy;
    private Analyst $testAnalyst;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AnalystPolicy();
        $this->testAnalyst = Analyst::factory()->create();
    }

    /** @test */
    public function super_admin_can_view_analysts()
    {
        $this->assertTrue($this->policy->view($this->superAdmin, $this->testAnalyst));
        $this->assertTrue($this->policy->reorder($this->superAdmin));
    }

    /** @test */
    public function governmental_official_can_view_analysts()
    {
        // Governmental Official has "show analyst" permission
        $this->assertTrue($this->policy->view($this->governmentalOfficial, $this->testAnalyst));
    }

    /** @test */
    public function governmental_official_cannot_create_update_delete_analysts()
    {
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testAnalyst));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testAnalyst));
    }

    /** @test */
    public function policy_maker_can_view_analysts()
    {
        // Policy Maker has "show analyst" permission
        $this->assertTrue($this->policy->view($this->policyMaker, $this->testAnalyst));
    }

    /** @test */
    public function policy_maker_cannot_create_update_delete_analysts()
    {
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testAnalyst));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testAnalyst));
    }

    /** @test */
    public function regular_user_cannot_view_analysts()
    {
        // Regular user has no "show analyst" permission
        $this->assertFalse($this->policy->view($this->regularUser, $this->testAnalyst));
    }

    /** @test */
    public function regular_user_cannot_create_update_delete_analysts()
    {
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testAnalyst));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testAnalyst));
    }

    /** @test */
    public function all_users_can_reorder_analysts()
    {
        // reorder() returns true for everyone in your implementation
        $this->assertTrue($this->policy->reorder($this->superAdmin));
        $this->assertTrue($this->policy->reorder($this->governmentalOfficial));
        $this->assertTrue($this->policy->reorder($this->policyMaker));
        $this->assertTrue($this->policy->reorder($this->regularUser));
    }
}