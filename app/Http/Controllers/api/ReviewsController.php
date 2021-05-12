<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Courses;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Validator;
use App\Http\Traits\ResponseTraits;

class ReviewsController extends Controller
{
    use ResponseTraits;
    public function store(Request $request){
        $validator = $this->validateReview($request);
        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        if (!Courses::find($request->course_id)->exists())
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
        $data["course_id"] = $request->course_id;
        $data["user_id"] = auth()->id();
        $data["comment"] = $request->comment;
        Reviews::create($data);

        return $this->successResponse(null, Utils::$MESSAGE_SUCCESS_ADDED);
    }


    public function deleteReview($review_id){
        if (!Reviews::where("user_id", auth()->id())->find($review_id)){
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
        }
        Reviews::find($review_id)->delete();
        return $this->successResponse(null, Utils::$MESSAGE_SMTH_DELETED);
    }

    public function validateReview(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "comment" => "required",
            "course_id" => 'required',
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

}
