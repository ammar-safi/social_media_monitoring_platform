<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hashtag extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function Posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, "hashtag_post", "hashtag_id", "post_id");
    }
}
