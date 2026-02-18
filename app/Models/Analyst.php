<?php

namespace App\Models;

use App\Enums\AnalystSentimentEnum;
use App\Enums\AnalystStanceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analyst extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $table = "analysis";

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'gov_id' => 'integer',
        "sentiment" => AnalystSentimentEnum::class,
        "stance" => AnalystStanceEnum::class,
    ];


    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    public function gov(): BelongsTo
    {
        return $this->belongsTo(GovOrg::class, "gov_id");
    }
}
