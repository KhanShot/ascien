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

    public function lessons(){
        return $this->hasMany("App\Models\Lessons", "section_id");
    }

}
