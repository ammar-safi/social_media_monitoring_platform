<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GovOrg extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = ["rating"];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function Posts(): HasMany
    {
        return $this->HasMany(Post::class, "gov_org_id");
    }
    public function Ratings(): HasMany
    {
        return $this->HasMany(Rating::class, "gov_org_id");
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
}
