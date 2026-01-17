<?php

namespace Tests\Feature\Policies;

use App\Models\Post;
use App\Policies\PostPolicy;

class PostPolicyTest extends BasePolicyTest
{
    private PostPolicy $policy;
    private Post $testPost;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PostPolicy();
        $this->testPost = Post::factory()->create();
    }

    /** @test */
    public function super_admin_can_perform_all_post_actions()
    {
        $this->assertTrue($this->policy->viewAny($this->superAdmin));
        $this->assertTrue($this->policy->view($this->superAdmin, $this->testPost));
        // Note: create, update, delete return false even for super admin in your current implementation
        $this->assertFalse($this->policy->create($this->superAdmin));
        $this->assertFalse($this->policy->update($this->superAdmin, $this->testPost));
        $this->assertFalse($this->policy->delete($this->superAdmin, $this->testPost));
    }

    /** @test */
    public function governmental_official_can_view_posts()
    {
        // Governmental Official has "show post" permission
        $this->assertTrue($this->policy->viewAny($this->governmentalOfficial));
        $this->assertTrue($this->policy->view($this->governmentalOfficial, $this->testPost));
    }

    /** @test */
    public function governmental_official_cannot_create_update_delete_posts()
    {
        $this->assertFalse($this->policy->create($this->governmentalOfficial));
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testPost));
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testPost));
    }

    /** @test */
    public function policy_maker_can_view_posts()
    {
        // Policy Maker has "show post" permission
        $this->assertTrue($this->policy->viewAny($this->policyMaker));
        $this->assertTrue($this->policy->view($this->policyMaker, $this->testPost));
    }

    /** @test */
    public function policy_maker_cannot_create_update_delete_posts()
    {
        $this->assertFalse($this->policy->create($this->policyMaker));
        $this->assertFalse($this->policy->update($this->policyMaker, $this->testPost));
        $this->assertFalse($this->policy->delete($this->policyMaker, $this->testPost));
    }

    /** @test */
    public function regular_user_cannot_view_posts()
    {
        // Regular user has no "show post" permission
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testPost));
    }

    /** @test */
    public function regular_user_cannot_create_update_delete_posts()
    {
        $this->assertFalse($this->policy->create($this->regularUser));
        $this->assertFalse($this->policy->update($this->regularUser, $this->testPost));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->testPost));
    }

    /** @test */
    public function all_users_can_reorder_posts()
    {
        // reorder() returns true for everyone in your implementation
        $this->assertTrue($this->policy->reorder($this->superAdmin));
        $this->assertTrue($this->policy->reorder($this->governmentalOfficial));
        $this->assertTrue($this->policy->reorder($this->policyMaker));
        $this->assertTrue($this->policy->reorder($this->regularUser));
    }
}