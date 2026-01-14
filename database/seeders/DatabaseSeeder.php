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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //TODO : use enum in type : admin
        if (!User::where("type", "admin")->exists()) {
            User::factory()->create([
                'first_name' => 'admin',
                'email' => 'ammar.ahmed.safi@gmail.com',
                "password" => Hash::make("123456"),
                "phone_number" => "0988845619",
                // TODO use enum
                'type' => "admin",
                "active" => 1,
            ]);
        }
        User::factory(10)->create();
        Analyst::factory(10)->create();
        ApproveUser::factory(10)->create();
        GovOrg::factory(10)->create();
        // Hashtag::factory(10)->create();
        // Post::factory(10)->create();
        HashtagPost::factory(10)->create();
        Invite::factory(10)->create();
        Rating::factory(10)->create();



    }
}
