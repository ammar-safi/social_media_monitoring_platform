<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gov_post', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("post_id");
            $table->unsignedBigInteger("gov_org_id");

            $table->foreign("post_id")->references("id")->on("posts");
            $table->foreign("gov_org_id")->references("id")->on("gov_orgs");
            $table->timestamps();

            $table->unique(["post_id", "gov_org_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gov_posts');
    }
};
