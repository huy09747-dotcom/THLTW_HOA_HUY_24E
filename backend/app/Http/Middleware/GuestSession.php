<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestSession
{
    public function handle(Request $r, Closure $next)
    {
        $token = $r->header('X-Guest-Token');
        abort_unless(is_string($token) && strlen($token) === 64, 401, 'Phiên giỏ hàng không hợp lệ.');
        $session = DB::table('guest_sessions')->where('token_hash', hash('sha256', $token))->where('expires_at', '>', now())->first();
        abort_unless($session, 401, 'Phiên giỏ hàng đã hết hạn. Vui lòng tải lại trang.');
        $r->attributes->set('guest_session', $session);

        return $next($r);
    }
}
