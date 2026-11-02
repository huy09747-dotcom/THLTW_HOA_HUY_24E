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
            if (!$user->username) {
                $user->username = 'user_'.strtolower((string) \Illuminate\Support\Str::ulid());
            }
            $user->roles = 'user';
            $user->status = 1;
            $user->save();
            $user->refresh();

            return response()->json([
                'message' => 'Dang ky thanh cong.',
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
            return response()->json(['message' => 'Email hoac mat khau khong dung.'], 401);
        }

        if ($user->status !== 1) {
            return response()->json(['message' => 'Tai khoan khong hoat dong.'], 403);
        }

        if (isset($data['audience']) && $user->roles !== $data['audience']) {
            return response()->json(['message' => 'Vui lòng đăng nhập đúng khu vực user hoặc admin.'], 403);
        }

        return response()->json([
            'message' => 'Dang nhap thanh cong.',
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('nguoidung', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('nguoidung', 'phone')->ignore($user->id)],
            'address' => 'nullable|string|max:255',
        ]);
        $user->fill($data)->save();
        return response()->json(['message' => 'Đã cập nhật hồ sơ.', 'user' => $user->fresh()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Dang xuat thanh cong.']);
    }
}
