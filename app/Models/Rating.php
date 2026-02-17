<?php

namespace App\Models;

use App\Casts\DecimalCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'gov_org_id' => 'integer',
        'rating' => DecimalCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function GovOrg(): BelongsTo
    {
        return $this->belongsTo(GovOrg::class, "gov_org_id");
    }

    public static function Color(string $rating)
    {
        if ($rating <= 1.5) {
            return "danger";
        } elseif ($rating < 3) {
            return "warning";
        } else {
            return "success";
        }
    }
}
