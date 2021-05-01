<?php

namespace App\Http\Middleware;

use App\Http\Traits\Utils;
use Closure;
use Illuminate\Http\Request;

use App\Http\Traits\ResponseTraits;

class TeacherProfileCompleted
{
    use ResponseTraits;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        if ($request->user() && $request->user()->is_teacher == 0)
//            return $next($request);
//            return response("yeeeeess");
        if ($request->user() && $request->user()->is_teacher == 1 && $request->user()->is_completed == 0)
        {
            $access_token = auth()->user()->createToken('authToken')->accessToken;
            $response["user"] = auth()->user();
            $response['token'] = $access_token;
            return $this->errorResponse("profile_not_completed", Utils::$MESSAGE_PROFILE_NOT_COMPLETED, $response);
        }
        return $next($request);


    }

}
