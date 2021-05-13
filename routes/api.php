<?php

use App\Models\StudentCoursesList;
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
    Route::post('/google/auth', 'api\AuthenticatesUsersController@googleAuth');

});
Route::post("profile/teacher/update", "api\UserProfileController@updateTeacherProfile")->middleware(['auth:api', "mustVerifyEmail"]);



Route::middleware(['auth:api',"mustVerifyEmail", "teacherProfileCompleted"])->group(function () {
    Route::get("user/info", "api\AuthenticatesUsersController@getUser");

    Route::post('/user/asTeacherAfterGmail/{value}/{email}', 'api\AuthenticatesUsersController@is_teacher');
    Route::post("/user/logout",'api\AuthenticatesUsersController@logout');

    Route::prefix("/profile")->group(function (){
        Route::post("/student/update", "api\UserProfileController@updateStudentProfile");
//        Route::post("/teacher/update", "api\UserProfileController@updateTeacherProfile");
    });



    Route::prefix("/teacher/courses/")->group(function (){
        Route::post("/store", "api\CourseController@store");
        Route::get("/all", "api\CourseController@getTeachersCourses");

        Route::get("/get/{course_id}", "api\CourseController@getCourse");


        Route::post("/create/lesson", "api\LessonsController@store");
        Route::post("/delete/lesson/{lesson_id}", "api\LessonsController@deleteLesson");

        Route::post("/create/section", "api\LessonsController@createSection");
        Route::get("/getLastSection/{course_id}", "api\LessonsController@getLastAddedSection");
        Route::post("/delete/section/{section_id}", "api\LessonsController@deleteSection");
        Route::post("/update/section/{section_id}", "api\LessonsController@updateSection");

    });

    Route::prefix("/student/")->group(function (){
        Route::prefix("/wishlist")->group(function (){
            Route::post("/store/{course_id}", "api\WishlistController@addToWishList");
            Route::get("/get", "api\WishlistController@getWishList");
            Route::post("/delete/{wish_id}", "api\WishlistController@deleteWish");
        });

        Route::prefix("/reviews")->group(function (){
            Route::post("/store", "api\ReviewsController@store");
            Route::post("/delete/{review_id}", "api\ReviewsController@deleteReview");
        });
        Route::prefix("/myCourses")->group(function (){
            Route::post("/watched/lesson", "api\StudentCoursesController@store");

            Route::get("/all", "api\StudentCoursesController@getMyCourses");
            Route::get("/get/{course_id}", "api\StudentCoursesController@getMyDetailCourse");
        });


        Route::post("/ratings/create", "api\RatingsController@store");
        Route::post("/generate/paymentUrl/{course_id}", "api\PaymentController@paymentUrl");
    });

});



Route::get("/courses/getTopCourses", "api\CourseController@getTopCourses");
Route::get("/courses/search", "api\CourseController@search");

Route::get("/courses/getOnlyCategories", "api\CourseController@getOnlyCategories");
Route::get("/courses/getDetailPublicCourse/{course_id}", "api\CourseController@getDetailPublicCourse");
Route::get("/courses/getCoursesByCategory/{category_id}", "api\CourseController@getCoursesByCategory");




