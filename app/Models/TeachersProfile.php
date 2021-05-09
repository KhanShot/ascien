<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachersProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "specialty", "education", "teaching_experience",
        "achievements", "birthday", "contacts", "about",
        "education_format", "videomakeing_experience", "auditory"
    ];


}
