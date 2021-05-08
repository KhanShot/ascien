<?php

namespace App\Http\Middleware;

use App\Http\Traits\Utils;
use Closure;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;

class MustVerifyEmail
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
        if (! $request->user() ||
            ($request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())) {
            return $this->errorResponse(Utils::$STATUS_CODE_EMAIL_NOT_VERIFIED, Utils::$MESSAGE_VERIFY_EMAIL,null);
        }
        return $next($request);
    }
}
