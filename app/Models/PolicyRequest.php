<?php

namespace App\Models;

use App\Enums\PolicyRequestEnum;
use App\Events\EmailEvent;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;

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
    public function GovWhoInvitePolicy(): HasOneThrough
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
        DB::beginTransaction();
        try {
            \Log::info("approving to all users");
            $requests = Self::query()
                ->where("status", PolicyRequestEnum::PENDING->value)
                ->where("expired_at", ">", Carbon::now())
                ->get();

            $requests->each->approve($admin_id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function approve($admin_id = null): bool
    {
        DB::beginTransaction();
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

                DB::commit();
                event(new EmailEvent($user, "Happy news !!  your account has been verified , you can access our site now", "Account approved"));
                return true;
            }
            return false;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
            return false;
        }
    }
    public function reject(): bool
    {
        DB::beginTransaction();
        try {

            $this->update([
                'status' => PolicyRequestEnum::APPROVED->value,
                'admin_id' => Filament::auth()->user()->id,
            ]);

            $user = User::find($this->user_id);

            if ($user) {
                DB::commit();
                event(new EmailEvent($user, "We have Bad news for you ,  your account has been rejected", "Account rejected"));
                return true;
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
            return false;
        }
    }
    public static function CheckExpiration()
    {
        DB::beginTransaction();
        try {
            $requests = self::query()
                ->where("expired_at", "<", Carbon::now())
                ->where("status", PolicyRequestEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "status" => PolicyRequestEnum::EXPIRED->value,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
        }
    }
}
