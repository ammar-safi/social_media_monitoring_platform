<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Admin(): BelongsTo
    {
        return $this->belongsTo(User::class, "admin_id");
    }
}
