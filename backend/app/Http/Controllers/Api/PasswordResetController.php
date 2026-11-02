<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\ResetPasswordLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgot(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        Password::broker('users')->sendResetLink(
            ['email' => $data['email'], 'status' => 1],
            function ($user, $token) {
                $user->notify(new ResetPasswordLink($token));
            }
        );
        return response()->json([
            'message' => 'Liên kết đặt lại mật khẩu đã được gửi vào Email. Vui lòng kiểm tra cả mục Spam.',
        ]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'token' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
        ], [
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);
        $status = DB::transaction(function () use ($data) {
            // Serialize reset attempts for the same account before validating the token.
            \App\Models\User::where('email', $data['email'])->lockForUpdate()->first();
            return Password::broker('users')->reset(
                array_merge($data, ['status' => 1]),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                    $user->tokens()->delete();
                }
            );
        });
        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Liên kết không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu liên kết mới.'], 422);
        }
        return response()->json(['message' => 'Đã đổi mật khẩu. Vui lòng đăng nhập bằng mật khẩu mới.']);
    }
}
