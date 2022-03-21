<?php

namespace marcusvbda\vstack\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;
use marcusvbda\vstack\Vstack;

class JwtAuth
{
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');
        $splited = explode(" ", $auth);
        $token = data_get($splited, "1", false);
        if (!$token) {
            abort(401, 'Unauthorized');
        }

        $decoded = Vstack::decodeJWT($token);
        if (!$decoded) {
            abort(401, 'Unauthorized');
        }
        Auth::loginUsingId($decoded->id);
        return $next($request);
    }
}
