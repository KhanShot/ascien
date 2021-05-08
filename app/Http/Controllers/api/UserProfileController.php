<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Storage;
use Validator;

class UserProfileController extends Controller
{
    use ResponseTraits;
    public function updateStudentProfile(Request $request){
        $validator = $this->validateStudentProfile($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }

        $user = User::find(auth()->id());

        $path = $user->avatar ?? "images/defaultAvatar.png";
        if ($request->hasFile("avatar")){

            if($user->avatar != $path){
                Storage::disk("s3")->delete($user->avatar);
            }
            $path = $request->file("avatar")->store("images", "s3");
            $user->avatar = $path;
        }

        if ($request->has("password"))
            $user->password = Hash::make($request->input("file"));
        if ($request->has("name"))
            $user->name = $request->input("name");

        $user->save();

        return $this->successResponse(["user"=>$user],Utils::$MESSAGE_USER_PROFILE_UPDATED);

    }

    public function updateTeacherProfile(Request $request){
        $user = User::find(auth()->id());


    }


    public function validateStudentProfile(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "name" => "min:3",
            "avatar" => 'mimes:jpeg,jpg,png,svg|max:3000',
            "password" => "min:6"
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }






//$url = Storage::disk('s3')->temporaryUrl(
//'file1.jpg', Carbon::now()->addMinutes(5)
//);



}
