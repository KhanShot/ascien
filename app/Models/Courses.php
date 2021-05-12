<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Courses extends Model
{
    use HasFactory;


    protected $fillable = [
        "user_id", "title", "category_id", "short_description", "language",
        "description", "level", "image", "intro_video", "requirements",
        "what_will_learn", "is_free", "price", "sale_price", "certificate", "status"
    ];

    public function category(){
        return $this->belongsTo("App\Models\CourseCategories", "category_id");
    }

    public function sections(){
        return $this->hasMany("App\Models\Sections", "course_id");
    }

    public function wishlist(){
        return $this->hasOne("App\Models\Wishlist", "course_id");
    }
}
