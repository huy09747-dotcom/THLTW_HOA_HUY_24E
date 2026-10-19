<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ActiveAccount
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->status !== 1) {
            return response()->json(['message' => 'Tài khoản đã bị khóa.'], 403);
        }
        return $next($request);
    }
}
