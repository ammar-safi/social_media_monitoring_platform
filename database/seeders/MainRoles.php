<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MainRoles extends Seeder
{

    protected $gov = "Governmental Official";
    protected $policy_mk =  "Policy Maker";
    protected $gov_permissions = [
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
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->GovRoles();
        $this->PolicyMkRoles();
    }


    public function GovRoles(): void
    {
        DB::beginTransaction();
        try {
            $gov_role = Role::where('name', $this->gov)->first();
            if (!$gov_role) {
                $gov_role = Role::create(['name' => $this->gov]);
            }
            $permissions_id = Permission::whereIn('name', $this->gov_permissions)->pluck('id')->toArray();
            $gov_role->syncPermissions($permissions_id);
            DB::commit();
            echo ".\n";
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
            echo ".\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
