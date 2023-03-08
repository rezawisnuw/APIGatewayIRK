<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{   
    public function handle($request, Closure $next)
  {
    return $next($request)
    ->header('Access-Control-Allow-Origin', '*')
    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, X-Token-Auth, Authorization, Cookie, X-Frame-Options')
    ->header('Access-Control-Allow-Credentials', 'true')
    ->header('Access-Control-Max-Age', '3600');
  }
}
