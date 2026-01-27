<?php

namespace App\Models;

use App\Enums\PolicyRequestEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "status" => PolicyRequestEnum::class,
    ];



    public function Admin(): BelongsTo
    {
        return $this->belongsTo(User::class, "admin_id");
    }
    public function Invite(): BelongsTo
    {
        return $this->belongsTo(Invite::class);
    }
}
