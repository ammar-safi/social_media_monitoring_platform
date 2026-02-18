<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Analyst;
use App\Models\ApproveUser;
use App\Models\GovOrg;
use App\Models\Hashtag;
use App\Models\HashtagPost;
use App\Models\Invite;
use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            MainRoles::class
        ]);
        // User::factory(10)->create();
        // Analyst::factory(10)->create();
        // ApproveUser::factory(10)->create();
        // GovOrg::factory(10)->create();
        // Hashtag::factory(10)->create();
        Hashtag::upsert([
            [
                'uuid' => Str::uuid(),
                "name" => "facebook",
                "user_id" => 1,
                "gov_id" => GovOrg::factory()->create()->id
            ],
            [
                'uuid' => Str::uuid(),
                "name" => "twitter",
                "user_id" => 1,
                "gov_id" => GovOrg::factory()->create()->id
            ]
        ], ["name"], ["uuid", "user_id", "gov_id"]);
        // Post::factory(10)->create();
        // HashtagPost::factory(10)->create();
        // Invite::factory(10)->create();
        // Rating::factory(10)->create();
    }
}
