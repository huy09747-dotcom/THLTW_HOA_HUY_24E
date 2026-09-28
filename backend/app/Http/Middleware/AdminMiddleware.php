<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->roles !== 'admin' || $user->status !== 1) {
            return response()->json(['message' => 'Chi admin moi duoc thuc hien thao tac nay.'], 403);
        }

        return $next($request);
    }
}
