<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStatusRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647']);

        return UserResource::collection(
            \App\Support\AdminFilters::apply(User::where('roles', 'user'), $request, ['name','email','phone'])->latest('id')->paginate(10)
        );
    }

    public function destroy(int $id)
    {
        DB::transaction(function() use($id) {
            $user=User::where('roles','user')->lockForUpdate()->findOrFail($id);
            abort_if($user->orders()->withTrashed()->exists(),422,'Thành viên đã có đơn hàng. Hãy khóa tài khoản thay vì xóa.');
            $user->tokens()->delete(); $user->delete();
        });
        return response()->json(['message'=>'Đã xóa thành viên.']);
    }

    public function show(int $id)
    {
        return new UserResource(User::where('roles', 'user')->findOrFail($id));
    }

    public function updateStatus(UserStatusRequest $request, int $id)
    {
        $user = DB::transaction(function () use ($request, $id) {
            $user = User::where('roles', 'user')->lockForUpdate()->findOrFail($id);
            $user->status = (int) $request->validated('status');
            $user->save();

            if ($user->status === 0) {
                $user->tokens()->delete();
            }

            return $user;
        });

        return (new UserResource($user))->additional([
            'message' => $user->status === 0 ? 'Da khoa tai khoan.' : 'Da mo khoa tai khoan.',
        ]);
    }

    public function orders(Request $request, int $id)
    {
        $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647']);
        $user = User::where('roles', 'user')->findOrFail($id);

        return response()->json($user->orders()
            ->select('id', 'user_id', 'code', 'total', 'payment_status', 'payment_method', 'status', 'created_at')
            ->latest('id')
            ->paginate(10));
    }
}
