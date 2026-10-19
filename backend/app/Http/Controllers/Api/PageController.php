<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\BaiViet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class PageController extends Controller
{
    private const PAGES = ['about' => 'Giới thiệu', 'purchase-policy' => 'Chính sách mua hàng', 'contact' => 'Liên hệ'];
    private function check(string $slug): void { abort_unless(array_key_exists($slug, self::PAGES), 404); }
    private function data(BaiViet $page): array { return $page->only(['id', 'title', 'slug', 'description', 'detail', 'status', 'updated_at']); }
    public function index()
    {
        $items = BaiViet::where('post_type', 'page')->whereIn('slug', array_keys(self::PAGES))->get()->keyBy('slug');
        $result = [];
        foreach (self::PAGES as $slug => $title) {
            $result[] = isset($items[$slug]) ? $this->data($items[$slug]) : ['id' => null, 'slug' => $slug, 'title' => $title, 'description' => '', 'detail' => '', 'status' => 2];
        }
        return response()->json(['data' => $result]);
    }
    public function show(string $slug)
    {
        $this->check($slug);
        $page = BaiViet::where('post_type', 'page')->where('slug', $slug)->where('status', 1)->firstOrFail();
        return response()->json(['data' => $this->data($page)]);
    }
    public function update(Request $request, string $slug)
    {
        $this->check($slug);
        $data = $request->validate([
            'title' => 'required|string|max:255', 'description' => 'nullable|string|max:2000',
            'detail' => 'required|string|max:100000', 'status' => ['required', 'integer', Rule::in([0, 1, 2])],
        ]);
        $page = BaiViet::withTrashed()->where('post_type', 'page')->where('slug', $slug)->first();
        if (!$page) {
            $request->validate(['slug' => 'prohibited']);
            abort_if(BaiViet::withTrashed()->where('slug', $slug)->exists(), 409, 'Đường dẫn đang được một bài viết sử dụng.');
            $page = new BaiViet(['slug' => $slug, 'post_type' => 'page']);
            $page->created_by = $request->user()->id;
        }
        $page->fill($data);
        $page->deleted_at = null;
        $page->updated_by = $request->user()->id;
        $page->save();
        return response()->json(['data' => $this->data($page), 'message' => 'Đã lưu trang.']);
    }
}
