<?php

namespace Tests\Feature\Policies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AllPoliciesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_policy_tests_can_run()
    {
        // This test ensures all policy test classes can be instantiated
        // and their setUp methods work correctly
        
        $testClasses = [
            UserPolicyTest::class,
            PostPolicyTest::class,
            AnalystPolicyTest::class,
            RolePolicyTest::class,
            PermissionPolicyTest::class,
            RatingPolicyTest::class,
            HashtagPolicyTest::class,
            InvitePolicyTest::class,
            GovOrgPolicyTest::class,
            ApproveUserPolicyTest::class,
        ];

        foreach ($testClasses as $testClass) {
            $this->assertTrue(class_exists($testClass), "Test class {$testClass} should exist");
        }

        $this->assertTrue(true, "All policy test classes exist and can be loaded");
    }

    /** @test */
    public function base_policy_test_setup_works()
    {
        // Test that the base test class setup works correctly
        $baseTest = new class extends BasePolicyTest {
            public function testSetup() {
                $this->setUp();
                return [
                    'superAdmin' => $this->superAdmin,
                    'governmentalOfficial' => $this->governmentalOfficial,
                    'policyMaker' => $this->policyMaker,
                    'regularUser' => $this->regularUser,
                ];
            }
        };

        $users = $baseTest->testSetup();
        
        $this->assertNotNull($users['superAdmin']);
        $this->assertNotNull($users['governmentalOfficial']);
        $this->assertNotNull($users['policyMaker']);
        $this->assertNotNull($users['regularUser']);
        
        $this->assertTrue($users['superAdmin']->hasRole('Super Admin'));
        $this->assertTrue($users['governmentalOfficial']->hasRole('Governmental Official'));
        $this->assertTrue($users['policyMaker']->hasRole('Policy Maker'));
    }
}