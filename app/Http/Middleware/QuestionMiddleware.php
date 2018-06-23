<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionMiddleware
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
        if (
            ! Auth::user()->isAdmin() &&
            ! Auth::user()->isDoctor() &&
            ! Auth::user()->isAssistant() &&
            ! Auth::user()->isSecretary()
        ) {

            if ($request->ajax()) {
                return new JsonResponse(null, 403);
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
