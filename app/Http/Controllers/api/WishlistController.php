<?php

namespace App\Http\Controllers\api;

use App\Http\Traits\Utils;
use App\Models\Courses;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTraits;
class WishlistController extends Controller
{
    use ResponseTraits;
    public function addToWishList($course_id){
        $course = Courses::find($course_id);
        if($course){
            if(Wishlist::where("user_id", auth()->id())->where("course_id", $course_id)->exists())
                return $this->errorResponse(Utils::$STATUS_CODE_ALREADY_EXISTS, Utils::$MESSAGE_ALREADY_EXISTS, null);

            Wishlist::create(["user_id"=>auth()->id(), "course_id" => $course_id]);

            return $this->successResponse(null, Utils::$MESSAGE_SUCCESS_ADDED);
        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
    }

    public function getWishList(){
        return Wishlist::with("courses")->where("user_id", auth()->id())->get();
    }

    public function deleteWish($wish_id): \Illuminate\Http\JsonResponse
    {
        $wishlist = Wishlist::find($wish_id);
        if ($wishlist){
            $wishlist->delete();
            return $this->successResponse(null, Utils::$MESSAGE_SMTH_DELETED );
        }
        return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
    }

}
