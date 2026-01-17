<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\UserPolicy;

class UserPolicyTest extends BasePolicyTest
{
    private UserPolicy $policy;
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
        $this->testUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_perform_all_user_actions()
    {
        $this->assertSuperAdminCanDoEverything(UserPolicy::class, $this->testUser);
    }

    /** @test */
    public function governmental_official_cannot_view_any_users()
    {
        $result = $this->policy->viewAny($this->governmentalOfficial);
        $this->assertFalse($result);
    }

    /** @test */
    public function governmental_official_cannot_view_specific_user()
    {
        $result = $this->policy->view($this->governmentalOfficial, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function governmental_official_cannot_create_users()
    {
        $result = $this->policy->create($this->governmentalOfficial);
        $this->assertFalse($result);
    }

    /** @test */
    public function governmental_official_cannot_update_users()
    {
        $result = $this->policy->update($this->governmentalOfficial, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function governmental_official_cannot_delete_users()
    {
        $result = $this->policy->delete($this->governmentalOfficial, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function policy_maker_cannot_view_any_users()
    {
        $result = $this->policy->viewAny($this->policyMaker);
        $this->assertFalse($result);
    }

    /** @test */
    public function policy_maker_cannot_view_specific_user()
    {
        $result = $this->policy->view($this->policyMaker, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function policy_maker_cannot_create_users()
    {
        $result = $this->policy->create($this->policyMaker);
        $this->assertFalse($result);
    }

    /** @test */
    public function policy_maker_cannot_update_users()
    {
        $result = $this->policy->update($this->policyMaker, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function policy_maker_cannot_delete_users()
    {
        $result = $this->policy->delete($this->policyMaker, $this->testUser);
        $this->assertFalse($result);
    }

    /** @test */
    public function regular_user_cannot_perform_any_user_actions()
    {
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testUser));
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testUser));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testUser));
    }
}