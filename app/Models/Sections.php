<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sections extends Model
{
    use HasFactory;

    protected $fillable = [
      "course_id", "title", "description"
    ];

    public $timestamps = false;

    public function lessons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("App\Models\Lessons", "section_id");
    }

    public function publicLessons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("App\Models\Lessons", "section_id");
    }

}
