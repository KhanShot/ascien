<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Models\Sections;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Models\Courses;
use App\Models\Lessons;
use Owenoj\LaravelGetId3\GetId3;

class LessonsController extends Controller
{
    use ResponseTraits;


    public function store(Request $request){
        $validator = $this->validateLesson($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        $data = $request->all();
        if ($request->hasFile("video_url")){
            $track = new GetId3(request()->file('video_url'));
            $path = $request->file("video_url")->store("courses/videos", "s3");
            $data["video_url"] = $path;
            $data["duration"] = $track->getPlaytime();
        }

        $data["description"] = $request->get("description", null);
        $data["resources"] = $request->has("resources") ? json_encode($request->get("resources")) : null ;

        Lessons::create($data);
        $course = $this->getDetailCourseTeacher($data["course_id"]);

        return $this->successResponse(["course"=>$course], Utils::$MESSAGE_COURSE_UPLOADED_SUCCESS);

    }

    public function createSection(Request $request){
        $validator = $this->validateSection($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }


        $data["title"] = $request->get("title");
        $data["course_id"] = $request->get("course_id");

        $data["description"] = $request->get("description", null);


        Sections::create($data);

        $course = $this->getDetailCourseTeacher($data["course_id"]);

        return $this->successResponse(["course"=>$course], Utils::$MESSAGE_COURSE_UPLOADED_SUCCESS);

    }

    public function getLastAddedSection($course_id){
        return $this->successResponse(["section"=>Sections::where("course_id", $course_id)->latest("id")->first()], Utils::$MESSAGE_COURSE_UPLOADED_SUCCESS);
    }



    function getDetailCourseTeacher($course_id){
        $course = Courses::where("user_id", auth()->id())->with("sections", "sections.lessons")->find($course_id);
        if ($course){
            return $course;
        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
    }

    public function validateSection(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "title" => "required | min:3 | max:50",
            "course_id" => 'required',
//            "description" => "required",
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


    public function validateLesson(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "title" => "required | min:3 | max:50",
            "section_id" => 'required',
            "course_id" => 'required',
            "content_type" => 'required',
            "video_url" => 'required_without:article_text|mimes:mp4,mov,ogg,qt | max:50000',
//            "presentation_file" => 'required_without:video_url,article_text',
            "article_text" => 'required_without:video_url',
//            "quiz_id" => 'required',
//            "description" => "required",
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }




    public function deleteLesson($lesson_id){
        $lesson = Lessons::find($lesson_id);
        if ($lesson){
            $this->localDeleteLesson($lesson);
            return $this->successResponse(["lesson_id" => $lesson_id], Utils::$MESSAGE_SMTH_DELETED);
        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null );
    }

    private function localDeleteLesson(Lessons $lesson){
        Storage::disk("s3")->delete($lesson->video_url);
        if ($lesson->content_type == "quiz")
            Quiz::find($lesson->quiz_id)->delete();
        $lesson->delete();
    }
    private function localDeleteSection(Sections $section){
        $lessons = Lessons::where("section_id",$section->id)->get("id");

        foreach ($lessons as $lesson){
            $this->localDeleteLesson($lesson);
        }
        $section->delete();
    }

    public function deleteSection($section_id){
        $section = Sections::find($section_id);

        if ($section){

            $this->localDeleteSection($section);
            return $this->successResponse(["section_id" => $section_id], Utils::$MESSAGE_SMTH_DELETED);

        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null );
    }

}
