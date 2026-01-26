<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use App\Enums\UserRoleEnum;
use App\Enums\UserTypeEnum;
use App\Events\EmailEvent;
use Exception;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasSuperAdmin;

    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserTypeEnum::class,
    ];
    protected $attributes = [
        'type' => UserTypeEnum::USER,
    ];
    protected $appends = ["name"];

    public function assignRoleBasedOnType(): void
    {
        // Remove existing roles first
        $this->syncRoles([]);

        if (!$this->type) {
            return;
        }

        // Assign role based on type
        match ($this->type) {
            UserTypeEnum::ADMIN        => $this->assignRole(UserRoleEnum::ADMIN->value),
            UserTypeEnum::USER         => $this->assignRole(UserRoleEnum::USER->value),
            UserTypeEnum::POLICY_MAKER => $this->assignRole(UserRoleEnum::POLICY_MAKER->value),
        };
    }


    public function getFilamentName(): string
    {
        return $this->first_name;
    }

    public function UsersApprovedByTheAdmin(): HasMany
    {
        return $this->hasMany(ApproveUser::class, "admin_id");
    }
    public function GetTheAdminWhoApproveMyAccount(): HasOne
    {
        return $this->hasOne(ApproveUser::class);
    }
    public function PolicyMakersThatUserInvites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }
    public function Ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
    public function Hashtags(): HasMany
    {
        return $this->hasMany(Hashtag::class);
    }
    public function PolicyRequest(): HasMany
    {
        return $this->hasMany(PolicyRequest::class , "admin_id");
    }
    public function getNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function ActivateAccount()
    {
        DB::beginTransaction();
        try {
            $this->update([
                "active" => 1
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
