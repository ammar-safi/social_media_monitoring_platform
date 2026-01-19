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

class User extends Authenticatable implements HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasSuperAdmin;

    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserTypeEnum::class,
    ];

    protected $attributes = [
        'type' => UserTypeEnum::USER,
    ];


    protected static function booted(): void
    {
        static::deleting(function ($user) {
            if ($user->type === UserTypeEnum::ADMIN) {
                throw new \Exception('you cannot delete this admin');
            }
        });


        static::saved(function ($user) {
            if ($user->wasRecentlyCreated || $user->wasChanged('type')) {
                // \Log::info("heelo froewfowpekfmlwekmf;we");
                $user->assignRoleBasedOnType();
            }
        });
    }

    public function assignRoleBasedOnType(): void
    {
        // Remove existing roles first
        $this->syncRoles([]);
        // \Log::info($this->type->value);

        if (!$this->type) {
            // \Log::info("there is no type");
            return;
        }
        // \Log::info("there is type", $this->type);

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
    //TODO : has many or has one ?
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
}
