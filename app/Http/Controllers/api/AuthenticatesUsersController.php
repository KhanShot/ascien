<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\URL;
use Validator;
use App\Http\Traits\ResponseTraits;
use App\Http\Traits\Utils;
use Illuminate\Foundation\Auth\VerifiesEmails;
Use App\Notifications\VerifyEmail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;




class AuthenticatesUsersController extends Controller
{
    use ResponseTraits, VerifiesEmails;

    public function register(Request $request){
        $validator = $this->registerValidate($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }
        $newUser = $request->all();
        $newUser['password'] = Hash::make($request->password);
        $newUser["is_completed"] = $request->has("is_teacher") ? 0 : 1;

        $user = User::create($newUser);

        $this->verificationSend($user);

        return $this->successResponse(["user"=>$user], Utils::$MESSAGE_VERIFY_EMAIL, Utils::$STATUS_CODE_EMAIL_NOT_VERIFIED);
    }

    public function verificationSend($user){

        $user->notify(new VerifyEmail($this->getSignedUrl($user->id, $user->email)));
    }


    public function verificationResend($email){
        $user = User::where("email", $email)->first();

        if (!$user){
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_EMAIL_NOT_FOUND, null);
        }
        if ($user->hasVerifiedEmail()){
            return $this->successResponse(["user"=>$user],Utils::$MESSAGE_EMAIL_VERIFIED_ALREADY);
        }
        $this->verificationSend($user);
        return $this->successResponse(null,Utils::$MESSAGE_VERIFY_EMAIL_SEND);
    }

    private function getSignedUrl($id, $email){
        return URL::temporarySignedRoute(
            'email.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $id,
                'hash' => sha1($email),
            ]
        );
    }

    public function verify(Request $request,$id, $hash){

        if(!$request->hasValidSignature()){
            return abort(404);
        }

        $user = User::find($id);

        if ($user->hasVerifiedEmail()){
            //return $this->successResponse($user,Utils::$MESSAGE_EMAIL_VERIFIED_ALREADY);
            return Utils::$MESSAGE_EMAIL_VERIFIED_ALREADY;
        }
        $user->markEmailAsVerified();

        //return $this->successResponse($user, Utils::$MESSAGE_EMAIL_VERIFIED);
        return  Utils::$MESSAGE_EMAIL_VERIFIED;

    }

    public function login(Request $request){
        $validator = $this->loginValidate($request);

        if (!empty($validator)){
            return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {

            if( is_null(auth()->user()->email_verified_at)){
                return $this->errorResponse(Utils::$STATUS_CODE_EMAIL_NOT_VERIFIED, Utils::$MESSAGE_VERIFY_EMAIL,null);
            }

            if ($request->user() && $request->user()->is_teacher == 1 && $request->user()->is_completed == 0)
            {
                $access_token = auth()->user()->createToken('authToken')->accessToken;
                $response["user"] = auth()->user();
                $response['token'] = $access_token;
                return $this->errorResponse("profile_not_completed", Utils::$MESSAGE_PROFILE_NOT_COMPLETED, null, $response);
            }

            $token = auth()->user()->createToken('authToken')->accessToken;
            $response["user"] = auth()->user();
            $response['token'] = $token;
            return $this->successResponse($response, Utils::$MESSAGE_AUTHENTICATED);

        } else {
            return $this->errorResponse(Utils::$STATUS_CODE_LOGIN_INCORRECT, Utils::$MESSAGE_LOGIN_INCORRECT,null);
        }
    }

     function loginValidate(Request $request)
     {
         $messages = $this->messages();

         $validator = Validator::make($request->all(), [
             "email" => "required",
             "password" => "required|min:5"
         ], $messages);
         if ($validator->fails()) {
             return $validator->errors();
         }
     }

    function registerValidate(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "name" => "required|min:3",
            "email" => "required|unique:users",
            "password" => "required|min:5"
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }

    }



    public function getUser(){
        return $this->successResponse(["user"=>\auth()->user()]);
    }

    public function logout(){
        $user = Auth::user()->token();
        $user->revoke();
        return $this->successResponse(null,Utils::$MESSAGE_USER_LOGOUT);
    }



    public function googleAuth(Request $request){

        if ($request->has("email")){
            $user = User::where("email", $request->email)->first();
//            return now();
            if ($user){
                if (is_null($user->socialite_id)){
                    $user->socialite_id = $request->google_id ?? null;
                    $user->socialite_key = "google";
                    $user->email_verified_at = now();
                    $user->save();

                }
                if (is_null($user->email_verified_at)){
                    $user->email_verified_at = now();
                    $user->save();
                }

                Auth::login($user);
                $token = auth()->user()->createToken('authToken')->accessToken;
                $response["user"] = auth()->user();
                $response['token'] = $token;
                return $this->successResponse($response, Utils::$MESSAGE_AUTHENTICATED);
            }else{
                $validator = $this->validateGoogleAuth($request);

                if (!empty($validator)){
                    return $this->errorResponse(Utils::$STATUS_CODE_HAS_INCORRECT_FIELDS, Utils::$MESSAGE_HAS_VALIDATION_ERRORS ,$validator);
                }
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'socialite_id'=> $request->google_id,
                    "socialite_key" => "google",
                    "email_verified_at" => now(),
                    'password' => Hash::make(Str::random(7))
                ]);

                Auth::login($newUser);

                $token = auth()->user()->createToken('authToken')->accessToken;
                $response["user"] = auth()->user();
                $response['token'] = $token;
                return $this->successResponse($response, Utils::$MESSAGE_AUTHENTICATED);
            }
        }
        return $this->errorResponse(Utils::$STATUS_CODE_LOGIN_INCORRECT, Utils::$MESSAGE_LOGIN_INCORRECT,null);


    }

    function validateGoogleAuth(Request $request){
        $messages = $this->messages();

        $validator = Validator::make($request->all(), [
            "name" => "required|min:3",
            "email" => "required|unique:users",
            "google_id" => "required"
        ], $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }

    }

    public function is_teacher($value, $email){
        if ($value == 1){
            $user = User::where("email", $email)->first();
            if ($user){
                $user->is_teacher = 1;
                $user->is_completed = 0;
                $user->save();

                Auth::login($user);
                $token = auth()->user()->createToken('authToken')->accessToken;
                $response["user"] = auth()->user();
                $response['token'] = $token;
                return $this->successResponse($response, Utils::$MESSAGE_USER_DEFINED_AS_TEACHER);
            }
            return $this->errorResponse(404, "not_found", null, null);
        }
        return $this->errorResponse(404, "some error occured", null, null);
    }

}
