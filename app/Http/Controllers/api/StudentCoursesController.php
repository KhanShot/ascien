<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Courses;
use App\Models\Lessons;
use App\Models\StudentCoursesList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Models\StudentCourses;
use Illuminate\Routing\Route;
use Validator;
use function PHPUnit\Framework\isEmpty;

class StudentCoursesController extends Controller
{
    use ResponseTraits;
    public function store(Request $request){
        $validator = $this->ValidateLessonsCheck($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }

        $data = StudentCourses::where("user_id", auth()->id())->where("lesson_id", $request->lesson_id)->first();

        if ($data)
            return $this->errorResponse(Utils::$STATUS_CODE_ALREADY_EXISTS, Utils::$MESSAGE_ALREADY_EXISTS, null);
        $fields['user_id'] = auth()->id();
        $fields["course_id"] = $request->course_id;
        $fields["lesson_id"] = $request->lesson_id;
        $sc = StudentCourses::create($fields);

        return $this->successResponse($sc, Utils::$MESSAGE_SUCCESS_ADDED);

    }


    public function getMyCourses(){
        $course_ids = StudentCoursesList::where("user_id", auth()->id())->pluck("course_id");

        $courses = Courses::with("sections.lessons.watched")->whereIn("id", $course_ids)->get();

        foreach ($courses as $course){
            $this->getProgressBySingleCourse($course);
        }

        return $courses;
    }

    public function getMyDetailCourse($course_id){
        $course = $this->getProgressBySingleCourse(Courses::with("sections.lessons.watched")->find($course_id));

        if (!$course)
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);

        if (!StudentCoursesList::where("user_id", auth()->id())->where("course_id", $course_id)->first() )
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);

        return $course;

    }

    private function getProgressBySingleCourse($course){

        $lesson_ids = StudentCourses::where("course_id", $course->id)->where("user_id", auth()->id())->pluck("lesson_id");
//        return $course->sections;
        if (sizeof($lesson_ids) == 0){
            $course["percentage"] = sizeof($lesson_ids);
            return $course;
        }


        $total = 0;
        $size = sizeof($lesson_ids);
        foreach ($course->sections as $section) {
//            echo $section . "_______________________________";
            foreach ($section->lessons as $lesson){
                $total++;
            }
        }

        $course["percentage"] = $size / $total;
        return $course;
//        return $size / $total;

    }

    public function ValidateLessonsCheck(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "course_id" => 'required|exists:courses,id',
            "lesson_id" => 'required|exists:lessons,id'
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


}
