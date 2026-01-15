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

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all model classes from the app/Models directory (including subfolders)
        $models = $this->getModels();

        // Create permissions for each model and action
        foreach ($models as $model) {
            $this->createPermissionsForModel($model);
        }

        $this->command->info('Permissions seeded successfully!');

        $this->SuperAdminRole();
    }

    /**
     * Get all models from the app/Models directory (including subfolders).
     *
     * @return array
     */
    protected function getModels()
    {
        $models = [];
        $modelFiles = File::allFiles(app_path('Models'));

        foreach ($modelFiles as $file) {
            // Get the relative path of the model file (including subfolders)
            $relativePath = $file->getRelativePathName();
            // Remove the file extension and replace slashes with namespace separators
            $modelClass = app()->getNamespace() . 'Models\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                $relativePath
            );

            // Check if the class exists
            if (class_exists($modelClass)) {
                $models[] = $modelClass;
            }
        }

        return $models;
    }

    /**
     * Create permissions for a specific model.
     *
     * @param string $model
     * @return void
     */
    protected function createPermissionsForModel($model)
    {

        $modelName = class_basename($model);

        foreach ($this->actions as $action) {
            $permissionName = strtolower("{$modelName}.{$action}");
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'sanctum')
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
