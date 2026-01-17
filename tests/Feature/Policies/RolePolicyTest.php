<?php

namespace Tests\Feature\Policies;

use App\Models\Role\Role;
use App\Policies\RolePolicy;

class RolePolicyTest extends BasePolicyTest
{
    private RolePolicy $policy;
    private Role $testRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RolePolicy();
        $this->testRole = Role::create(['name' => 'Test Role']);
    }

    /** @test */
    public function super_admin_can_perform_all_role_actions()
    {
        $this->assertSuperAdminCanDoEverything(RolePolicy::class, $this->testRole);
    }

    /** @test */
    public function governmental_official_cannot_perform_any_role_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->view($this->governmentalOfficial, $this->testRole));
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testRole));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testRole));
    }

    /** @test */
    public function policy_maker_cannot_perform_any_role_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->policyMaker));
        $this->assertFalse($this->policy->view($this->policyMaker, $this->testRole));
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testRole));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testRole));
    }

    /** @test */
    public function regular_user_cannot_perform_any_role_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testRole));
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testRole));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testRole));
    }
}