<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\BaiViet;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private function query()
    {
        return BaiViet::where('post_type', 'post')->with('chude');
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
            'search' => 'nullable|string|max:255',
        ]);
        $query = $this->query();
        if (!empty($data['search'])) {
            $query->where('title', 'like', '%' . $data['search'] . '%');
        }

        return PostResource::collection($query->latest('id')->paginate(10));
    }

    public function options()
    {
        return response()->json(['topics' => \App\Models\ChudeBaiViet::orderBy('name')->get(['id', 'name'])]);
    }

    public function store(PostRequest $request)
    {
        $item = new BaiViet($request->validated());
        $item->post_type = 'post';
        $item->created_by = $request->user()->id;
        $item->save();

        return (new PostResource($item))->response()->setStatusCode(201);
    }

    public function update(PostRequest $request, int $id)
    {
        $item = $this->query()->findOrFail($id);
        $item->fill($request->validated());
        $item->post_type = 'post';
        $item->updated_by = $request->user()->id;
        $item->save();

        return new PostResource($item);
    }

    public function destroy(int $id)
    {
        $this->query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Da chuyen vao thung rac.']);
    }

    public function trash(Request $request)
    {
        $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647']);

        return PostResource::collection($this->query()->onlyTrashed()->latest('id')->paginate(10));
    }

    public function restore(int $id)
    {
        $this->query()->onlyTrashed()->findOrFail($id)->restore();

        return response()->json(['message' => 'Khoi phuc thanh cong.']);
    }

    public function forceDelete(int $id)
    {
        $this->query()->onlyTrashed()->findOrFail($id)->forceDelete();

        return response()->json(['message' => 'Da xoa vinh vien.']);
    }
}
