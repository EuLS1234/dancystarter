<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(auth()->user()?->language ?? session('locale', 'en'));
//        if(auth()->check())
//        {
//            app()->setLocale(auth()->user()->language);
//        }
//        elseif(session()->has('locale')){
//            app()->setLocale(session('locale'));
//        }
        return $next($request);
    }
}
