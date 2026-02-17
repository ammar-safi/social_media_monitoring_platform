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
        Schema::disableForeignKeyConstraints();

        Schema::create('analysis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('post_id');
            $table->foreign('post_id')->references('id')->on('posts');

            $table->unsignedBigInteger('gov_id');
            $table->foreign('gov_id')->references('id')->on('gov_orgs');

            $table->enum('sentiment', ["positive", "negative", "normal"])->nullable();
            $table->string('sentiment_confidence')->nullable();
            $table->enum('stance', ["supportive", "neutral", "against"])->nullable();
            $table->string("stance_confidence")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['post_id', 'gov_id'], 'analysis_post_gov_unique');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis');
    }
};
