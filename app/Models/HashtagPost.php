<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HashtagPost extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    protected $table = "hashtag_post";
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'hashtag_uuid' => 'string',
            'post_uuid' => 'string',
        ];
    }

    public function hashtag(): BelongsTo
    {
        return $this->belongsTo(Hashtag::class, "uuid", "hashtag_uuid");
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, "uuid", "post_uuid");
    }
}
