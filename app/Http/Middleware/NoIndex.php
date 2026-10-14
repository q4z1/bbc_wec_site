<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoIndex
{
    /**
     * Keeps search engines out of non-production instances (test sites).
     * Switched on per instance with APP_NOINDEX=true in .env, so the code
     * stays identical on prod and test.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('app.noindex')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
