<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lessons extends Model
{
    use HasFactory;

    protected $fillable = [
      "section_id", "title", "description", "content_type", "video_url",
        "presentation_file", "article_text", "resources", "quiz_id", "duration"
    ];

    public function watched(){
        return $this->hasOne("App\Models\StudentCourses", "lesson_id");
    }


}
