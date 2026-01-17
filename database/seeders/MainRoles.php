<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Role\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MainRoles extends Seeder
{

    protected $user = UserRoleEnum::USER;
    protected $policy_mk =  UserRoleEnum::POLICY_MAKER;
    protected $user_permissions = [
        "show user",
        "update user",

        "show organization",

        "show analyst",

        "show rating",
        "create rating",
        "update rating",
        "delete rating",

        "show comment",
        "create comment",
        "update comment",
        "delete comment",

        "show hashtag",
        "create hashtag",
        "delete hashtag",

        "show invite",
        "create invite",
        "update invite",
        "delete invite",

        "show post",
    ];
    protected $policy_mk_permissions = [
        "show analyst",

        "show rating",
        "create rating",
        "update rating",
        "delete rating",

        "show comment",
        "create comment",
        "update comment",
        "delete comment",

        "show post",
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->userRoles();
        $this->PolicyMkRoles();
    }


    public function userRoles(): void
    {
        DB::beginTransaction();
        try {
            $user_role = Role::where('name', $this->user)->first();
            if (!$user_role) {
                $user_role = Role::create(['name' => $this->user]);
            }
            $permissions_id = Permission::whereIn('name', $this->user_permissions)->pluck('id')->toArray();
            $user_role->syncPermissions($permissions_id);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function PolicyMkRoles(): void
    {
        DB::beginTransaction();
        try {
            $policy_role = Role::where('name', $this->policy_mk)->first();
            if (!$policy_role) {
                $policy_role = Role::create(['name' => $this->policy_mk]);
            }
            $permissions_id = Permission::whereIn('name', $this->policy_mk_permissions)->pluck('id')->toArray();
            $policy_role->syncPermissions($permissions_id);
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
