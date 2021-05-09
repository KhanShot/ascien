<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Auth::routes(['verify' => true]);

Route::prefix('user')->group(function (){
    Route::post("/register", 'api\AuthenticatesUsersController@register');
    Route::post("/login", 'api\AuthenticatesUsersController@login');
    Route::post("/verify/email/resend/{email}", "api\AuthenticatesUsersController@verificationResend");

    Route::get('/verify/email/{id}/{hash}', 'api\AuthenticatesUsersController@verify')->name("email.verify");

    //google Auth
    Route::get('/google/redirect', 'api\AuthenticatesUsersController@redirectToGoogle');
    Route::get('/google/callback', 'api\AuthenticatesUsersController@handleGoogleCallback');

});



Route::middleware(['auth:api',"mustVerifyEmail", "teacherProfileCompleted"])->group(function () {
    Route::get("user/info", "api\AuthenticatesUsersController@getUser");

    Route::post('/user/asTeacherAfterGmail/{value}/{email}', 'api\AuthenticatesUsersController@is_teacher');
    Route::post("/user/logout",'api\AuthenticatesUsersController@logout');

    Route::prefix("/profile")->group(function (){
        Route::post("/student/update", "api\UserProfileController@updateStudentProfile");
//        Route::post("/teacher/update", "api\UserProfileController@updateTeacherProfile");
    });


    Route::prefix("/teacher/courses")->group(function (){
        Route::post("/store", "api\CourseController@store");
//        Route::post("/teacher/update", "api\UserProfileController@updateTeacherProfile");
    });

});



Route::post("profile/teacher/update", "api\UserProfileController@updateTeacherProfile")->middleware(['auth:api', "mustVerifyEmail"]);
