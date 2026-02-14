<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GovOrg extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = ["rating", "my_rating", "my_comment"];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function Posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            "post_id",
            "gov_post",
            "gov_org_id",
            "id",
            "id"
        );
    }
    public function Ratings(): HasMany
    {
        return $this->HasMany(Rating::class, "gov_org_id");
    }
    public function hashtag(): HasMany
    {
        return $this->hasMany(Hashtag::class, "gov_id");
    }

    public function getRatingAttribute()
    {
        $rating = $this->ratings()->get("rating");
        if ($rating->count() == 0) {
            return $this->rating = 0;
        }
        $rating = $rating->sum("rating") / $rating->count();

        return $this->rating = $rating;
    }
    public static function IsThereRating($id)
    {
        $rating = self::find($id)->ratings()->count();

        if ($rating) {
            return true;
        }
        return false;
    }
    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->maxLength(255),
        ];
    }

    public function getMyRatingAttribute()
    {
        return Rating::where("user_id", Filament::auth()->user()?->id)->where("gov_org_id", $this->id)->get("rating")->first()?->rating;
    }
    public function getMyCommentAttribute()
    {
        return Rating::where("user_id", Filament::auth()->user()?->id)->where("gov_org_id", $this->id)->get("comment")->first()?->comment;
    }
}
