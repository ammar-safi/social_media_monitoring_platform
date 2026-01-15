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

class User extends Authenticatable implements HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes ,HasRoles;


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
    ];


    protected static function booted(): void
    {
        static::deleting(function ($user) {
            if ($user->id === 1) {
                throw new \Exception('you cannot delete this admin');
            }
        });
    }

    public function getFilamentName() :string {
        return $this->first_name;
    }

    public function UsersApprovedByTheAdmin() : HasMany {
        return $this->hasMany(ApproveUser::class , "admin_id");
    }
    //TODO : has many or has one ?
    public function GetTheAdminWhoApproveMyAccount() : HasOne {
        return $this->hasOne(ApproveUser::class);
    }
    public function PolicyMakersThatUserInvites() : HasMany {
        return $this->hasMany(Invite::class);
    }
    public function Ratings() : HasMany {
        return $this->hasMany(Rating::class);
    }

    
    
}
