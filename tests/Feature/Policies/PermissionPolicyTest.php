<?php

namespace Tests\Feature\Policies;

use App\Models\Role\Permission;
use App\Policies\PermissionPolicy;

class PermissionPolicyTest extends BasePolicyTest
{
    private PermissionPolicy $policy;
    private Permission $testPermission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PermissionPolicy();
        $this->testPermission = Permission::create(['name' => 'test permission']);
    }

    /** @test */
    public function super_admin_can_perform_all_permission_actions()
    {
        $this->assertSuperAdminCanDoEverything(PermissionPolicy::class, $this->testPermission);
    }

    /** @test */
    public function governmental_official_cannot_perform_any_permission_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->view($this->governmentalOfficial, $this->testPermission));
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testPermission));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testPermission));
    }

    /** @test */
    public function policy_maker_cannot_perform_any_permission_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->policyMaker));
        $this->assertFalse($this->policy->view($this->policyMaker, $this->testPermission));
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testPermission));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testPermission));
    }

    /** @test */
    public function regular_user_cannot_perform_any_permission_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testPermission));
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testPermission));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testPermission));
    }
}