<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Courses extends Model
{
    use HasFactory;


    protected $fillable = [
        "user_id", "title", "category_id", "short_description", "language",
        "description", "level", "image", "intro_video", "requirements",
        "what_will_learn", "is_free", "price", "sale_price", "certificate", "status"
    ];

    public function instructor(){
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function category(){
        return $this->belongsTo("App\Models\CourseCategories", "category_id");
    }

    public function sections(){
        return $this->hasMany("App\Models\Sections", "course_id");
    }

    public function wishlist(){
        return $this->hasOne("App\Models\Wishlist", "course_id");
    }
    public function reviews(){
        return $this->hasMany("App\Models\Reviews", "course_id");
    }

    public function ratings(){
        return $this->hasMany("App\Models\Ratings", "course_id")
//            ->selectRaw("avg(rating) as avgRating, course_id");
        ->selectRaw("rating, course_id")
            ->groupBy("course_id");
    }



}
