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
use App\Models\TeachersProfile;


class UserProfileController extends Controller
{
    use ResponseTraits;
    public function updateStudentProfile(Request $request){
        $validator = $this->validateStudentProfile($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }

        $user = User::find(auth()->id());

        $this->updateMainFields($request, $user);
        $user->save();
        return $this->successResponse(["user"=>$user],Utils::$MESSAGE_USER_PROFILE_UPDATED);

    }

    public function validateStudentProfile(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "name" => "min:3",
            "avatar" => 'mimes:jpeg,jpg,png|max:3000',
            "password" => "min:6"
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    public function updateTeacherProfile(Request $request){
        $validator = $this->validateTeacherProfile($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        $user = User::with("teacher_profile")->find(auth()->id());


//        $user = User::find(auth()->id());
        $this->updateMainFields($request, $user);

        $contacts = [
            "phone" => $request->get("phone") ?? null,
            "youtube" => $request->get("youtube") ?? null,
            "insta" => $request->get("insta") ?? null,
            "facebook" => $request->get("facebook") ?? null,
            "linedln" => $request->get("linedln") ?? null
        ];
        $data = $request->except("phone");
        $data["user_id"] = $user->id;
        $data['contacts'] = json_encode($contacts);

        if (!TeachersProfile::where("user_id", $user->id)->first()){
            if(TeachersProfile::create($data)){
                $user->is_completed = 1;
                $user->save();
            }
        }else{
            TeachersProfile::where("user_id", $user->id)->update($data);
            $user->is_completed = 1;
            $user->save();
        }

        return $this->successResponse(["user"=>$user], Utils::$MESSAGE_USER_PROFILE_UPDATED);

    }

    public function validateTeacherProfile(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "education_format" => "required",
            "videomakeing_experience" => 'required',
            "auditory" => 'required',
            "birthday" => 'date',
            "education" => 'required',
            "phone" => 'required',
            "specialty" => "required"

        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }


    function updateMainFields(Request $request, $user){
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


    }






//$url = Storage::disk('s3')->temporaryUrl(
//'file1.jpg', Carbon::now()->addMinutes(5)
//);



}
