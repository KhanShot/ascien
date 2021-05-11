<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\CourseCategories;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Models\Courses;
use Illuminate\Support\Facades\Storage;
use Validator;

class CourseController extends Controller
{
    use ResponseTraits;
    public function store(Request $request){
//        $data = $request->except(["image", "intro_video"]);
        $validator = $this->validateCourse($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        $data = $request->except(["image", "intro_video"]);
        $data = $this->uploadFiles($request, $data);
        $data = $this->setPrice($request, $data);

//        return $data;

        $data["user_id"] = auth()->id();
        $data["what_will_learn"] = json_encode($data['what_will_learn']);
        $data["requirements"] = json_encode($data['requirements']);


        $course = Courses::create($data);
        return $this->successResponse(["course"=>$course], Utils::$MESSAGE_COURSE_UPLOADED_SUCCESS);
    }

    public function getOnlyCategories(){
        return CourseCategories::all();
    }
    public function validateCourse(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "title" => "required | min:3 | max:50",
            "category_id" => 'required',
            "short_description" => "required",
            "language" => "required",
            "description" => "required",
            "level" => "required",
            "image" => 'required|mimes:jpeg,jpg,png,svg|max:3000',
            "intro_video" => "required|mimes:mp4,mov,ogg,qt | max:50000",
            "requirements" => "required",
            "what_will_learn" => "required",
//            "is_free" => "required",
//            "price" => "required",
//            "sale_price" => "required",
//            "certificate" => "required",
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


    function uploadFiles(Request $request, $data){
        if ($request->hasFile("image")){
            $path = $request->file("image")->store("courses/images", "s3");
            $data['image'] = $path;
        }

        if ($request->hasFile("intro_video")){
            $path = $request->file("intro_video")->store("courses/videos", "s3");
            $data['intro_video'] = $path;
        }
        return $data;
    }

    function setPrice(Request $request, $data){
        if(!$request->has("price")){
            $data["is_free"] = 1;
        }
        if ($request->has("price")){
            $data["price"] = $request->get("price", 0);
            $data["sale_price"] = $request->get("sale_price") ?? $data["price"];
        }
        return $data;
    }

}
