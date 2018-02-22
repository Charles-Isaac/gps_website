<?php

namespace App\Http\Middleware;

use Closure;

class MustHaveNoSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('sessionId')) {
            return redirect('/truck/session');
        }
        return $next($request);
    }
}
