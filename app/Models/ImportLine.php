<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLine extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "import_line";
}
