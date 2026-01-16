<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Role\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Role\Role;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    // Define the actions you want to create permissions for
    protected $actions = ['create', 'update', 'show', 'delete'];
    protected $permissions = ['user', "organization", "analyst", "rating", "comment"];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for each role and action
        foreach ($this->permissions as $permission) {
            $this->createPermissionsForRole($permission);
        }

        $this->command->info('Permissions seeded successfully!');

        $this->SuperAdminRole();
        
    }

    /**
     * Create permissions for a specific $role.
     *
     * @param string $role
     * @return void
     */
    protected function createPermissionsForRole($role)
    {
        foreach ($this->actions as $action) {
            $permissionName = strtolower("{$action} {$role}");
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();

            if (!$permission) {
                $permission = Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
    public function SuperAdminRole()
    {
        DB::beginTransaction();
        try {
            // Create Super Admin Role
            $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
            if (!$superAdminRole) {
                $superAdminRole = Role::Create(['name' => 'Super Admin']);
            }

            $masterUser = User::where('email', 'ammar.ahmed.safi@gmail.com')->first();
            if (!$masterUser) {
                // Create Master User
                $masterUser = User::Create(
                    [
                        'first_name' => 'admin',
                        'email' => 'ammar.ahmed.safi@gmail.com',
                        "password" => Hash::make("123456"),
                        "phone_number" => "0988845619",
                        'type' => UserTypeEnum::ADMIN,
                        "active" => 1,
                    ]
                );
            }

            // Assign Super Admin role to the Master user
            $masterUser->assignRole($superAdminRole);

            DB::commit();

            echo "Master role, account, and permissions created successfully.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
   
}
