<?php

namespace Tests\Feature\Policies;

use App\Models\GovOrg;
use App\Policies\GovOrgPolicy;

class GovOrgPolicyTest extends BasePolicyTest
{
    private GovOrgPolicy $policy;
    private GovOrg $testGovOrg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new GovOrgPolicy();
        $this->testGovOrg = GovOrg::factory()->create();
    }

    /** @test */
    public function super_admin_can_view_organizations()
    {
        $this->assertTrue($this->policy->viewAny($this->superAdmin));
        $this->assertTrue($this->policy->view($this->superAdmin, $this->testGovOrg));
    }

    /** @test */
    public function super_admin_cannot_create_update_delete_organizations()
    {
        // Even Super Admin cannot create/update/delete in your implementation
        $this->assertFalse($this->policy->create($this->superAdmin));
        $this->assertFalse($this->policy->update($this->superAdmin, $this->testGovOrg));
        $this->assertFalse($this->policy->delete($this->superAdmin, $this->testGovOrg));
    }

    /** @test */
    public function governmental_official_can_view_organizations()
    {
        // Governmental Official has "show organization" permission
        $this->assertTrue($this->policy->viewAny($this->governmentalOfficial));
        $this->assertTrue($this->policy->view($this->governmentalOfficial, $this->testGovOrg));
    }

    /** @test */
    public function governmental_official_cannot_create_update_delete_organizations()
    {
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testGovOrg));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testGovOrg));
    }

    /** @test */
    public function policy_maker_cannot_view_organizations()
    {
        // Policy Maker does NOT have "show organization" permission
        $this->assertFalse($this->policy->viewAny($this->policyMaker));
        $this->assertFalse($this->policy->view($this->policyMaker, $this->testGovOrg));
    }

    /** @test */
    public function policy_maker_cannot_create_update_delete_organizations()
    {
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testGovOrg));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testGovOrg));
    }

    /** @test */
    public function regular_user_cannot_access_organizations()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testGovOrg));
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testGovOrg));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testGovOrg));
    }

    /** @test */
    public function no_one_can_reorder_organizations()
    {
        // reorder() returns false for everyone in your implementation
        $this->assertFalse($this->policy->reorder($this->superAdmin));
        $this->assertFalse($this->policy->reorder($this->governmentalOfficial));
        $this->assertFalse($this->policy->reorder($this->policyMaker));
        $this->assertFalse($this->policy->reorder($this->regularUser));
    }

    /** @test */
    public function no_one_can_delete_any_organizations()
    {
        $this->assertFalse($this->policy->deleteAny($this->superAdmin));
        $this->assertFalse($this->policy->deleteAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->deleteAny($this->policyMaker));
        $this->assertFalse($this->policy->deleteAny($this->regularUser));
    }
}