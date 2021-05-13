<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCoursesList extends Model
{
    use HasFactory;
    protected $fillable = [
      "user_id", "course_id", "payment_id", "is_gift", "gift_from"
    ];

    public function courses(){
        return $this->belongsTo("App\Models\Courses");
    }

}
