<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function ids(Request $request)
    {
        return response()->json(['data'=>DB::table('favorites')->where('user_id',$request->user()->id)->pluck('product_id')]);
    }

    public function index(Request $request)
    {
        $request->validate(['page'=>'sometimes|integer|min:1|max:2147483647']);
        $products=Product::withReviewSummary()->with(['category','brand'])->where('status',1)
            ->whereIn('id', DB::table('favorites')->where('user_id',$request->user()->id)->select('product_id'))
            ->orderByDesc('id')->paginate(10);
        return ProductResource::collection($products);
    }

    public function store(Request $request, int $id)
    {
        Product::where('status',1)->findOrFail($id);
        DB::table('favorites')->insertOrIgnore(['user_id'=>$request->user()->id,'product_id'=>$id,'created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['message'=>'Đã lưu sản phẩm yêu thích.']);
    }

    public function destroy(Request $request, int $id)
    {
        DB::table('favorites')->where('user_id',$request->user()->id)->where('product_id',$id)->delete();
        return response()->json(['message'=>'Đã bỏ yêu thích.']);
    }
}
