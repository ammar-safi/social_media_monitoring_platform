<?php

namespace App\Models;

use App\Enums\ApproveUserStatusEnum;
use App\Events\NotifyUserEvent;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportConsoleCommands\Commands\Upgrade\ThirdPartyUpgradeNotice;
use Spatie\FlareClient\Flare;

class ApproveUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'id' => 'integer',
        'admin_id' => 'integer',
        'user_id' => 'integer',
        'expired_at' => 'timestamp',
        'expired' => 'boolean',
        'status' => ApproveUserStatusEnum::class,
    ];


    public function Admin(): BelongsTo
    {
        return $this->belongsTo(User::class, "admin_id");
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function approveAll($admin_id = null)
    {
        try {
            Log::info("approving to all users");
            $requests = Self::query()
                ->where("status", ApproveUserStatusEnum::PENDING->value)
                ->where("expired", 0)
                ->where("expired_at", ">", Carbon::now())
                ->get();

            $requests->each->approve($admin_id);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function approve($admin_id = null): bool
    {
        try {
            $this->update([
                'status' => ApproveUserStatusEnum::APPROVED->value,
                'admin_id' => $admin_id ?? Filament::auth()->user()?->id,
            ]);

            $user = User::find($this->user_id);

            if ($user) {
                $user->update([
                    "active" => 1
                ]);

                event(new NotifyUserEvent(
                    user_name: $user->name,
                    email: $user->email,
                    subject: "Account approved",
                    message: "Happy news !!  your account has been verified , you can access our site now"
                ));
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }
    public function reject(): bool
    {
        try {
            $this->update([
                'status' => ApproveUserStatusEnum::REJECTED->value,
                'admin_id' => Filament::auth()->user()->id,
            ]);

            $user = User::find($this->user_id);

            if ($user) {
                event(new NotifyUserEvent(
                    user_name: $user->name,
                    email: $user->email,
                    subject: "Account rejected",
                    message: "Bad news,  your account has been rejected"
                ));
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }
}
