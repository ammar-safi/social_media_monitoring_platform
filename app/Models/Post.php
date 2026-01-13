<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function govOrg(): BelongsTo
    {
        return $this->belongsTo(GovOrg::class , 'gov_org_id');
    }
}
