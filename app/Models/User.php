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
use Filament\Forms;
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

    protected static function booted(): void
    {
        static::deleting(function ($user) {
            if ($user->type === UserTypeEnum::ADMIN) {
                throw new \Exception('you cannot delete this admin');
            }
        });

        static::deleted(function ($user) {
            $user->update([
                "email" => $user->email . ".deleted"
            ]);
        });


        static::saved(function ($user) {
            if ($user->wasRecentlyCreated || $user->wasChanged('type')) {
                $user->assignRoleBasedOnType();
            }
        });
    }

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
    public function getNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function sendEmail($messageContent , $subject="New message")
    {
        Mail::send('email.email', [
            'recipientName' => $this->first_name,
            'messageContent' => $messageContent
        ], function ($message) use ($subject) {
            $message->to($this->email)->subject($subject);
        });
    }
}
