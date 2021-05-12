<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Ratings;
use App\Models\Reviews;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use Validator;

class RatingsController extends Controller
{
    use ResponseTraits;
    public function store(Request $request){
        $validator = $this->validateRatings($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        $rating = Ratings::where("user_id", auth()->id())->where("course_id", $request->course_id)->first();

        if ($rating){
            $rating->rating = $request->rating;
            $rating->save();
            return $this->successResponse(null, Utils::$MESSAGE_DATA_HAS_BEEN_MODIFIED);
        }

        $data["user_id"] = auth()->id();
        $data["course_id"] = $request->course_id;
        $data["rating"] = $request->rating;

        Ratings::create($data);

        return $this->successResponse(null, Utils::$MESSAGE_SUCCESS_ADDED);

    }



    private function validateRatings(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "course_id" => 'required|exists:courses,id',
            "rating" => "required|integer|max:5"
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


}
