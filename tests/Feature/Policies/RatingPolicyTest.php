<?php

namespace Tests\Feature\Policies;

use App\Models\Rating;
use App\Policies\RatingPolicy;

class RatingPolicyTest extends BasePolicyTest
{
    private RatingPolicy $policy;
    private Rating $testRating;
    private Rating $userOwnedRating;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RatingPolicy();
        
        // Create a rating owned by someone else
        $this->testRating = Rating::factory()->create([
            'user_id' => $this->regularUser->id
        ]);
        
        // Create a rating owned by governmental official
        $this->userOwnedRating = Rating::factory()->create([
            'user_id' => $this->governmentalOfficial->id
        ]);
    }

    /** @test */
    public function super_admin_can_view_all_ratings()
    {
        $this->assertTrue($this->policy->viewAny($this->superAdmin));
        $this->assertTrue($this->policy->view($this->superAdmin, $this->testRating));
    }

    /** @test */
    public function super_admin_can_create_ratings()
    {
        $this->assertTrue($this->policy->create($this->superAdmin));
    }

    /** @test */
    public function super_admin_can_update_any_rating()
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue($this->policy->update($this->superAdmin, $this->testRating));
        $this->assertTrue($this->policy->update($this->superAdmin, $this->userOwnedRating));
    }

    /** @test */
    public function super_admin_can_delete_any_rating()
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue($this->policy->delete($this->superAdmin, $this->testRating));
        $this->assertTrue($this->policy->delete($this->superAdmin, $this->userOwnedRating));
    }

    /** @test */
    public function governmental_official_can_view_ratings()
    {
        // Governmental Official has "show rating" permission
        $this->assertTrue($this->policy->viewAny($this->governmentalOfficial));
        $this->assertTrue($this->policy->view($this->governmentalOfficial, $this->testRating));
    }

    /** @test */
    public function governmental_official_can_create_ratings()
    {
        // Governmental Official has "create rating" permission
        $this->assertTrue($this->policy->create($this->governmentalOfficial));
    }

    /** @test */
    public function governmental_official_can_update_own_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertTrue($this->policy->update($this->governmentalOfficial, $this->userOwnedRating));
    }

    /** @test */
    public function governmental_official_cannot_update_others_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertFalse($this->policy->update($this->governmentalOfficial, $this->testRating));
    }

    /** @test */
    public function governmental_official_can_delete_own_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertTrue($this->policy->delete($this->governmentalOfficial, $this->userOwnedRating));
    }

    /** @test */
    public function governmental_official_cannot_delete_others_ratings()
    {
        $this->actingAs($this->governmentalOfficial);
        $this->assertFalse($this->policy->delete($this->governmentalOfficial, $this->testRating));
    }

    /** @test */
    public function policy_maker_can_view_ratings()
    {
        // Policy Maker has "show rating" permission
        $this->assertTrue($this->policy->viewAny($this->policyMaker));
        $this->assertTrue($this->policy->view($this->policyMaker, $this->testRating));
    }

    /** @test */
    public function policy_maker_can_create_ratings()
    {
        // Policy Maker has "create rating" permission
        $this->assertTrue($this->policy->create($this->policyMaker));
    }

    /** @test */
    public function regular_user_cannot_view_ratings()
    {
        // Regular user has no "show rating" permission
        $this->assertFalse($this->policy->viewAny($this->regularUser));
        $this->assertFalse($this->policy->view($this->regularUser, $this->testRating));
    }

    /** @test */
    public function regular_user_cannot_create_ratings()
    {
        $this->assertFalse($this->policy->create($this->regularUser));
    }

    /** @test */
    public function all_users_can_reorder_ratings()
    {
        $this->assertTrue($this->policy->reorder($this->superAdmin));
        $this->assertTrue($this->policy->reorder($this->governmentalOfficial));
        $this->assertTrue($this->policy->reorder($this->policyMaker));
        $this->assertTrue($this->policy->reorder($this->regularUser));
    }

    /** @test */
    public function no_one_can_delete_any_ratings()
    {
        $this->assertFalse($this->policy->deleteAny($this->superAdmin));
        $this->assertFalse($this->policy->deleteAny($this->governmentalOfficial));
        $this->assertFalse($this->policy->deleteAny($this->policyMaker));
        $this->assertFalse($this->policy->deleteAny($this->regularUser));
    }
}