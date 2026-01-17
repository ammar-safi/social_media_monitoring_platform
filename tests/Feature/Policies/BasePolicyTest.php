<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Models\Role\Role;
use App\Models\Role\Permission;
use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class BasePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $governmentalOfficial;
    protected User $policyMaker;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders to set up roles and permissions
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'MainRoles']);
        
        $this->createTestUsers();
    }

    protected function createTestUsers(): void
    {
        // Create Super Admin
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@test.com',
            'type' => UserTypeEnum::ADMIN,
        ]);
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $this->superAdmin->assignRole($superAdminRole);

        // Create Governmental Official
        $this->governmentalOfficial = User::factory()->create([
            'email' => 'gov@test.com',
            'type' => UserTypeEnum::ADMIN,
        ]);
        $govRole = Role::where('name', 'Governmental Official')->first();
        $this->governmentalOfficial->assignRole($govRole);

        // Create Policy Maker
        $this->policyMaker = User::factory()->create([
            'email' => 'policy@test.com',
            'type' => UserTypeEnum::POLICY_MAKER,
        ]);
        $policyRole = Role::where('name', 'Policy Maker')->first();
        $this->policyMaker->assignRole($policyRole);

        // Create Regular User (no specific role)
        $this->regularUser = User::factory()->create([
            'email' => 'user@test.com',
            'type' => UserTypeEnum::USER,
        ]);
    }

    protected function assertSuperAdminCanDoEverything(string $policyClass, $model = null): void
    {
        $policy = new $policyClass();
        
        if (method_exists($policy, 'viewAny')) {
            $this->assertTrue($policy->viewAny($this->superAdmin));
        }
        
        if (method_exists($policy, 'view') && $model) {
            $this->assertTrue($policy->view($this->superAdmin, $model));
        }
        
        if (method_exists($policy, 'create')) {
            $this->assertTrue($policy->create($this->superAdmin));
        }
        
        if (method_exists($policy, 'update') && $model) {
            $this->assertTrue($policy->update($this->superAdmin, $model));
        }
        
        if (method_exists($policy, 'delete') && $model) {
            $this->assertTrue($policy->delete($this->superAdmin, $model));
        }
    }
}