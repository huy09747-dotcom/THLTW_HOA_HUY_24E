<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = new User($request->validated());
            $user->roles = 'user';
            $user->status = 1;
            $user->save();
            $user->refresh();

            return response()->json([
                'message' => 'Đăng ký thành công.',
                'user' => $user,
                'token' => $user->createToken('api')->plainTextToken,
                'token_type' => 'Bearer',
            ], 201);
        });
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        }

        if ($user->status !== 1) {
            return response()->json(['message' => 'Tài khoản không hoạt động.'], 403);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Đăng xuất thành công.']);
    }
}
