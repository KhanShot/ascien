<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Courses;
use App\Models\StudentCoursesList;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Models\StudentCourses;

class StudentCoursesController extends Controller
{
    use ResponseTraits;
    public function store(Request $request){
        $validator = $this->ValidateLessonsCheck($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
    }


    public function getMyCourses(){
        $course_ids = StudentCoursesList::where("user_id", auth()->id())->pluck("course_id");

        $courses = Courses::with("sections.lessons")->whereIn("id", $course_ids)->get();

        return $courses;
    }


    public function ValidateLessonsCheck(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "title" => "required | min:3 | max:50",
            "category_id" => 'required',
            "short_description" => "required",
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


}
