<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use App\Models\Courses;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\URL;


class PaymentController extends Controller
{
    use ResponseTraits;

    public function index(Request $request, $user_id, $course_id){

//        if ($request->hasValidSignature()){
            $data["user_id"] = $user_id;
            $data["course"] = Courses::find($course_id);

            return view("payments.index", compact("data") );
//        }
        return abort(404);
    }


    public function paymentUrl($course_id){
        $user = User::find(auth()->id());
        $course = Courses::find($course_id);
        if (!$course){
            return $this->errorResponse(Utils::$STATUS_CODE_NOT_FOUND, Utils::$MESSAGE_DATA_NOT_FOUND, null);
        }
        $payment = Payment::where("user_id", auth()->id())->where("course_id", $course_id)->first();
        if ($payment)
            return $this->errorResponse(Utils::$STATUS_CODE_ALREADY_EXISTS, Utils::$MESSAGE_ALREADY_EXISTS, null);

        if ($course->is_free == 1)
            return $this->errorResponse(Utils::$STATUS_CODE_ALREADY_EXISTS, Utils::$MESSAGE_ALREADY_EXISTS, null);


        $url = URL::temporarySignedRoute("payment", now()->addMinutes(5), ["user_id" => $user->id, "course_id" => $course_id]);

        return $url;
    }


    public function makePayment(Request $request){
        $data["user_id"] = $request->user_id;
        $data["course_id"] = $request->course_id;
        $data["payment_method"] = "mastercard";
        $data["price"] = $request->price;

//        dd($data);
        Payment::create($data);
        return redirect()->route("payment.success");


    }
    public function success(){
        return view("payments.success");
    }

}
