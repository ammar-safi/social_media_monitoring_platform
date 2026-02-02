<?php

namespace App\Models;

use App\Enums\PolicyRequestEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
    public function GovWhoInvitePolicy():HasOneThrough
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
        return $this->belongsTo(user::class , "policy_id");
    }
}
