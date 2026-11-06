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
use Illuminate\Support\Facades\Storage;

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

    public function changePassword(Request $request): JsonResponse
    {
        $data=$request->validate(['current_password'=>'required|string|max:255','password'=>'required|string|min:8|max:255|confirmed']);
        abort_unless(Hash::check($data['current_password'],$request->user()->password),422,'Mật khẩu hiện tại không đúng.');
        DB::transaction(function() use($request,$data) {
            $user=User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            abort_unless(Hash::check($data['current_password'],$user->password),422,'Mật khẩu đã thay đổi, vui lòng thử lại.');
            $user->password=$data['password']; $user->save();
            $user->tokens()->where('id','!=',$request->user()->currentAccessToken()->id)->delete();
        });
        return response()->json(['message'=>'Đã đổi mật khẩu và đăng xuất các phiên khác.']);
    }

    public function updateProfileAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.required' => 'Vui lòng chọn ảnh đại diện.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Chỉ hỗ trợ ảnh JPG, PNG hoặc WebP.',
            'image.max' => 'Ảnh đại diện tối đa 5 MB.',
        ]);

        $user = $request->user();
        $oldPath = str_starts_with((string) $user->avatar, '/storage/')
            ? substr($user->avatar, strlen('/storage/'))
            : null;
        $path = $request->file('image')->store('images/avatars/'.$user->id, 'public');
        abort_unless($path, 500, 'Không thể lưu ảnh đại diện.');

        $user->avatar = '/storage/'.$path;
        $user->save();

        if ($oldPath && str_starts_with($oldPath, 'images/avatars/'.$user->id.'/')) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json(['message' => 'Đã cập nhật ảnh đại diện.', 'user' => $user->fresh()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Dang xuat thanh cong.']);
    }
}
