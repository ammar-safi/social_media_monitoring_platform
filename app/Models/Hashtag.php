<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Hashtag extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'uuid' => 'string',
            'name' => 'string',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();
            $user->user_id = Filament::auth()->user()->id;
        });
    }

    public function Posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, "hashtag_post", "hashtag_uuid", "post_uuid", "uuid", "uuid");
    }
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
