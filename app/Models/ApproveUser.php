<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApproveUser extends Model
{
    use HasFactory, SoftDeletes;

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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
