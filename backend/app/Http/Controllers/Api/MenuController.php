<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    private function query()
    {
        return Menu::query();
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
            'search' => 'nullable|string|max:255',
        ]);
        $query = $this->query();
        if (!empty($data['search'])) {
            $query->where('name', 'like', '%' . $data['search'] . '%');
        }

        return MenuResource::collection($query->orderBy('sort_order')->orderBy('id')->paginate(10));
    }

    public function publicIndex()
    {
        $items=Menu::where('status',1)->orderBy('sort_order')->orderBy('id')->get()->filter(function($item){
            if($item->type==='custom')return preg_match('~^/(?!/)[^\\\\\s]*$~u',$item->link) || (filter_var($item->link,FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($item->link,PHP_URL_SCHEME)),['http','https']));
            $model=match($item->type){'category'=>\App\Models\Category::class,'brand'=>\App\Models\Brand::class,'topic'=>\App\Models\ChudeBaiViet::class,'page'=>\App\Models\BaiViet::class,default=>null};
            $target=$model ? $model::where('status',1)->find($item->table_id) : null;
            if(!$target || ($item->type==='page' && $target->post_type!=='page'))return false;
            $item->link=match($item->type){'category'=>'/products?category='.$target->id,'brand'=>'/products?brand='.$target->id,'topic'=>'/news?topic='.rawurlencode($target->slug),'page'=>'/'.rawurlencode($target->slug)};
            return true;
        })->values();
        return MenuResource::collection($items);
    }

    public function options()
    {
        return response()->json([
            'category' => \App\Models\Category::orderBy('name')->get(['id', 'name', 'slug']),
            'brand' => \App\Models\Brand::orderBy('name')->get(['id', 'name', 'slug']),
            'topic' => \App\Models\ChudeBaiViet::orderBy('name')->get(['id', 'name', 'slug']),
            'page' => \App\Models\BaiViet::where('post_type', 'page')->orderBy('title')->get(['id', 'title as name', 'slug']),
        ]);
    }

    public function store(MenuRequest $request)
    {
        $item = new Menu($request->validated());
        if ($item->type === 'custom') {
            $item->table_id = null;
        }
        $item->created_by = $request->user()->id;
        $item->save();

        return (new MenuResource($item))->response()->setStatusCode(201);
    }

    public function update(MenuRequest $request, int $id)
    {
        $item = $this->query()->findOrFail($id);
        $item->fill($request->validated());
        if ($item->type === 'custom') {
            $item->table_id = null;
        }
        $item->updated_by = $request->user()->id;
        $item->save();

        return new MenuResource($item);
    }

    public function destroy(int $id)
    {
        $this->query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Da chuyen vao thung rac.']);
    }

    public function trash(Request $request)
    {
        $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647']);

        return MenuResource::collection($this->query()->onlyTrashed()->latest('id')->paginate(10));
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
