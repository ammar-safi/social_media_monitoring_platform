<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PolicyRequestEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('policy_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("admin_id")->nullable();
            $table->foreign("admin_id")->references("id")->on("users");

            $table->unsignedBigInteger("policy_id")->nullable();
            $table->foreign("policy_id")->references("id")->on("users");

            $table->unsignedBigInteger("invite_id");
            $table->foreign("invite_id")->references("id")->on("invites");
            
            $table->enum(
                "status",
                [
                    PolicyRequestEnum::APPROVED->value,
                    PolicyRequestEnum::REJECTED->value,
                    PolicyRequestEnum::PENDING->value,
                    PolicyRequestEnum::EXPIRED->value,
                ]
            )->default(PolicyRequestEnum::PENDING->value);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_requests');
    }
};
