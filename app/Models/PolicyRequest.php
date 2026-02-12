<?php

namespace App\Models;

use App\Enums\PolicyRequestEnum;
use App\Events\NotifyUserEvent;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PolicyRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "status" => PolicyRequestEnum::class,
    ];



    public function AdminWhoApprovedRequest(): BelongsTo
    {
        return $this->belongsTo(User::class, "admin_id");
    }
    public function Invite(): BelongsTo
    {
        return $this->belongsTo(Invite::class);
    }
    public function UserWhoInvitePolicy(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Invite::class,
            "id",
            "id",
            "invite_id",
            "user_id"
        );
    }
    public function PolicyMaker(): BelongsTo
    {
        return $this->belongsTo(user::class, "policy_id");
    }

    public function approveAll($admin_id = null)
    {
        try {
            Log::info("approving to all users");
            $requests = Self::query()
                ->where("status", PolicyRequestEnum::PENDING->value)
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
                'status' => PolicyRequestEnum::APPROVED->value,
                'admin_id' => $admin_id ?? Filament::auth()->user()?->id,
            ]);

            $user = User::find($this->policy_id);

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
        }
    }
    public function reject(): bool
    {
        try {

            $this->update([
                'status' => PolicyRequestEnum::REJECTED->value,
                'admin_id' => Filament::auth()->user()->id,
            ]);

            $user = User::find($this->policy_id);

            if ($user) {
                event(new NotifyUserEvent(
                    user_name: $user->name,
                    email: $user->email,
                    subject: "Account rejected",
                    message: "Bad news ,  your account has been rejected"
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
