<?php

namespace App\Models;

use App\Enums\ApproveUserStatusEnum;
use App\Events\EmailEvent;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportConsoleCommands\Commands\Upgrade\ThirdPartyUpgradeNotice;
use Spatie\FlareClient\Flare;

class ApproveUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'admin_id' => 'integer',
            'user_id' => 'integer',
            'expired_at' => 'timestamp',
            'expired' => 'boolean',
        ];
    }


    public function Admin(): BelongsTo
    {
        return $this->belongsTo(User::class, "admin_id");
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function approveAll()
    {
        DB::beginTransaction();
        try {

            $requests = $this->query()
                ->where("status", ApproveUserStatusEnum::PENDING->value)
                ->where("expired", 0)
                ->where("expired_at", ">", Carbon::now())
                ->get();

            $requests->each->approve();
        } catch (Exception $e) {
            Notification::make()
                ->warning()
                ->title("Error")
                ->body("Something went wrong , pleas try again")
                ->send();
        }

        Notification::make()
            ->success()
            ->title("Approved")
            ->body("All requests was approved")
            ->send();


        DB::commit();
    }

    public function approve(): bool
    {
        DB::beginTransaction();
        try {
            $this->update([
                'status' => ApproveUserStatusEnum::APPROVED->value,
                'admin_id' => Filament::auth()->user()->id,
            ]);

            $user = User::find($this->user_id);

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
            \Log::info($e->getMessage());
            return false;
        }
    }
    public function reject(): bool
    {
        DB::beginTransaction();
        try {

            $this->update([
                'status' => ApproveUserStatusEnum::APPROVED->value,
                'admin_id' => Filament::auth()->user()->id,
            ]);

            $user = User::find($this->user_id);

            if ($user) {
                DB::commit();
                event(new EmailEvent($user, "We have Bad news for you ,  your account has been rejected", "Account rejected"));
                return true;
            }

            return false;
        } catch (Exception $e) {
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
                ->where("status", ApproveUserStatusEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "expired" => 1,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
