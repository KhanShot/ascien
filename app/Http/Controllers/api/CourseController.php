<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\CourseCategories;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Models\Courses;
use Illuminate\Support\Collection;
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

        $data["user_id"] = auth()->id();
        $data["what_will_learn"] = json_encode($data['what_will_learn']);
        $data["requirements"] = isset($data["requirements"]) ? json_encode($data['requirements']) : null;

        $course = Courses::create($data);

        return $this->successResponse(["course"=>$course], Utils::$MESSAGE_COURSE_UPLOADED_SUCCESS);
    }

    public function getCourse($course_id){
        return Courses::with("sections.lessons")->where("user_id", auth()->id())->find($course_id);
    }

    public function getTeachersCourses(){
        return Courses::where("user_id", auth()->id())->get();
    }

    public function getOnlyCategories(){
        return CourseCategories::withCount("courses")->get();
    }

    public function getTopCourses(){
        return $this->getAvgRatingsFromCourses(Courses::with("ratings", "instructor")->get());
    }

    public function getAvgRatingsFromCourses(Collection $courses){
        foreach ($courses as $course){

            if ($course->rating){
                return $course->ratings;
            }
            $course["avgRating"] = $this->getAvgRatingsFromSingleCourse($course->ratings ?? array());
        }

        return $courses;
    }

    public function search(Request $request){
        $courses = Courses::with("ratings", "instructor")->where("category_id", "!=" ,0);

        if ($request->has("category_id"))
            $courses->where("category_id", $request->get("category_id"));

        if ($request->has("q"))
            $courses->where('title', 'like', "%". $request->get("q") .'%');

        if ($request->has("price")){
            if ($request->price == "free")
                $courses->where("is_free", 1);
            if ($request->price == "paid")
                $courses->where("is_free", 0);
        }
        //wishlist search
//
//        if (auth()){
//
//        }

        //reviews
//        if ($request->has("reviews")){
//            $courses->orderBy("reviews", "desc");
//        }




//        dd($courses->get());

        return $this->getAvgRatingsFromCourses($courses->get());
    }


    public function getCoursesByCategory($category_id){
//        return CourseCategories::with("courses")->find($category_id);
        return Courses::where("category_id", $category_id)->get();
    }

    public function getDetailPublicCourse(Request  $request, $course_id){

        $course = Courses::with(["sections.lessons"=>function($query){
            $query->select("id", "title", "content_type", "description", "duration" , "section_id");
        }, "reviews", "ratings", "instructor"])->find($course_id);

        if ($course){
//            $course->ratings = isset($course->ratings[0]) ? $course->ratings[0]->avgRating : null;
            $course["avgRating"] = $this->getAvgRatingsFromSingleCourse($course->ratings??array());;
            return $course;
        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
    }

    private function getAvgRatingsFromSingleCourse($ratings = array()){
        $total = 0;
        foreach ($ratings as $rating){
            $total = $total + $rating->rating;
        }
        if (sizeof($ratings) == 0)
            return null;
        return $total / sizeof($ratings);
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
            "image" => 'required|mimes:jpeg,jpg,png|max:3000',
            "intro_video" => "required|mimes:mp4,mov | max:50000",
//            "requirements" => "required",
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
