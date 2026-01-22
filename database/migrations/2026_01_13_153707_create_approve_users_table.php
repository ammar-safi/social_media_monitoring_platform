<?php

use App\Enums\ApproveUserStatusEnum;
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

        Schema::create('approve_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('users');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('expired_at');
            $table->boolean('expired')->default(0);
            $table->enum('status', [ApproveUserStatusEnum::PENDING->value, ApproveUserStatusEnum::REJECTED->value, ApproveUserStatusEnum::APPROVED->value])->default(ApproveUserStatusEnum::PENDING->value);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approve_users');
    }
};
