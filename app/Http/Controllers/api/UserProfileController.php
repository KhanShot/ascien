<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{

    public function updateStudentProfile(Request $request){
        $user = User::find(auth()->id());

        $path = $request->file("avatar")->store("images", "s3");

        return $path;



    }



}
