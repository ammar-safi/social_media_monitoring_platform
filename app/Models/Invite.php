<?php

namespace App\Models;

use App\Enums\InviteStatusEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invite extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'expired_at' => 'timestamp',
            'expired' => 'boolean',
            'status' => InviteStatusEnum::class
        ];
    }

    protected static function booted()
    {
        static::created(function (Invite $invite) {
            $invite->update([
                "expired_at" => Carbon::now()->addDays(config("approve_expired", 5))
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function PolicyRequest(): HasOne
    {
        return $this->hasOne(PolicyRequest::class);
    }
    
    public static function CheckExpiration()
    {
        DB::beginTransaction();
        try {
            $requests = self::query()
                ->where("expired_at", "<", Carbon::now())
                ->where("status", InviteStatusEnum::PENDING->value)
                ->get();
            foreach ($requests as $request) {
                $request->update([
                    "status" => InviteStatusEnum::EXPIRED->value,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
        }
    }
}
