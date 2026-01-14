<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'gov_org_id' => 'integer',
        ];
    }

    public function Post() : HasOne {
        return $this->hasOne(Post::class);
    }

    public function govOrg(): BelongsTo
    {
        return $this->belongsTo(GovOrg::class , 'gov_org_id');
    }
    
    public function Hashtags () : BelongsToMany {
        return $this->belongsToMany(Hashtag::class , "hashtag_post" , "post_id" , "hashtag_id");
    }
}
