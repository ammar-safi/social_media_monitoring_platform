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
use App\Enums\UserTypeEnum;
use Filament\Forms;

class User extends Authenticatable implements HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes ,HasRoles, HasSuperAdmin;

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
    ];


    protected static function booted(): void
    {
        static::deleting(function ($user) {
            if ($user->type === UserTypeEnum::ADMIN) {
                throw new \Exception('you cannot delete this admin');
            }
        });


        static::created(function ($user) {
            $user->assignRoleBasedOnType();
        });
        
        static::updated(function ($user) {
            if ($user->wasChanged('type')) {
                $user->assignRoleBasedOnType();
            }
        });
    }

    public function assignRoleBasedOnType(): void
    {
        // Remove existing roles first
        $this->syncRoles([]);
        
        // Assign role based on type
        match($this->type) {
            UserTypeEnum::ADMIN => $this->assignRole('Super Admin'),
            UserTypeEnum::USER => $this->assignRole('Governmental Official'),
            UserTypeEnum::POLICY_MAKER => $this->assignRole('Policy Maker'),
        };
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
    public function Hashtags() : HasMany {
        return $this->hasMany(Hashtag::class);
    }

    public static function getForm () {
        return [
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->required(),
        ];
    }
    
}
