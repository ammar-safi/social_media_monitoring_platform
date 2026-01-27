<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
