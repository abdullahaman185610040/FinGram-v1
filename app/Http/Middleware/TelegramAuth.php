<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TelegramAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('telegram_id')) {
            return redirect('/login');
        }

        return $next($request);
    }
}