<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
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
        $data["user_id"] = auth()->id();
        $data["what_will_learn"] = json_encode($data['what_will_learn']);
        $data["requirements"] = json_encode($data['requirements']);


        if ($request->hasFile("image")){
            $path = $request->file("image")->store("courses/images", "s3");
            $data['image'] = $path;
        }

        if ($request->hasFile("intro_video")){
            $path = $request->file("intro_video")->store("courses/videos", "s3");
            $data['intro_video'] = $path;
        }
//        Courses::create($data);
        return $data;
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
            "image" => 'mimes:jpeg,jpg,png,svg|max:3000',
            "intro_video" => "mimes:mp4,mov,ogg,qt | max:50000",
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



}
