<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchasePolicySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('roles', 'admin')->where('status', 1)->firstOrFail();
        $page = BaiViet::where('slug', 'purchase-policy')->where('post_type', 'page')->first()
            ?? new BaiViet(['slug' => 'purchase-policy', 'post_type' => 'page']);
        $page->fill([
            'title' => 'Chính sách mua hàng',
            'description' => 'Mua sắm rõ ràng, an tâm lựa chọn. Tìm hiểu cách đặt hàng, thanh toán, nhận hàng và yêu cầu hỗ trợ tại HH Shop.',
            'status' => 1,
            'detail' => <<<'TEXT'
1. Thông tin sản phẩm
Vui lòng kiểm tra tên, cấu hình, màu sắc, hình ảnh, phụ kiện đi kèm và tình trạng hàng trước khi đặt mua. Nếu chưa chắc sản phẩm có phù hợp với thiết bị của mình hay không, hãy liên hệ HH Shop để được tư vấn trước khi chốt đơn.

2. Giá bán và chương trình ưu đãi
Giá bán và mức giảm được hiển thị trên từng sản phẩm. Tổng tiền cần thanh toán được thể hiện tại bước xác nhận đơn hàng. Mã giảm giá chỉ áp dụng khi đáp ứng điều kiện của chương trình; hãy kiểm tra mức giảm thực tế trước khi đặt đơn. Nội dung quảng cáo sự kiện cần được đối chiếu với thời gian và danh sách sản phẩm áp dụng.

3. Hướng dẫn đặt hàng
Đăng nhập tài khoản → chọn sản phẩm và số lượng → thêm vào giỏ hàng → kiểm tra giỏ → nhập thông tin nhận hàng → chọn phương thức thanh toán → xác nhận đặt hàng. Sau khi tạo đơn, vào mục Đơn hàng để xem mã đơn, sản phẩm, tổng tiền và trạng thái xử lý.

4. Thông tin người nhận
Cung cấp đầy đủ họ tên, số điện thoại liên lạc và địa chỉ nhận hàng. Kiểm tra lại thông tin trước khi xác nhận để tránh giao nhầm hoặc chậm xử lý. Nếu phát hiện sai sót, hãy liên hệ hỗ trợ kèm mã đơn để cửa hàng kiểm tra khả năng điều chỉnh.

5. Thanh toán
Chọn phương thức thanh toán đang khả dụng tại trang đặt hàng. Với COD, thanh toán khi nhận hàng theo thông tin đơn được xác nhận. Với thanh toán trực tuyến, kiểm tra đúng số tiền và mã đơn trước khi thao tác. Tạo đơn thành công chưa đồng nghĩa đã thanh toán; hãy theo dõi trạng thái trong tài khoản. Nếu giao dịch chưa được ghi nhận, liên hệ hỗ trợ trước khi thanh toán lại.

6. Xác nhận và giao nhận
HH Shop xử lý đơn dựa trên tình trạng hàng và thông tin người nhận. Phí vận chuyển được thể hiện trong tổng tiền nếu có. Lịch giao và các thay đổi liên quan cần được cửa hàng xác nhận theo từng đơn. Khi nhận hàng, kiểm tra bao bì, số lượng và sản phẩm; lưu lại hình ảnh nếu có dấu hiệu móp méo, thiếu hàng hoặc giao sai.

7. Thay đổi hoặc hủy đơn
Nếu muốn thay đổi thông tin hoặc hủy đơn, liên hệ sớm qua số 0398 716 527 và cung cấp mã đơn. Khả năng xử lý phụ thuộc vào trạng thái chuẩn bị và giao hàng. Không tự mặc định đơn đã được hủy khi chưa nhận được xác nhận của cửa hàng.

8. Đổi trả và hoàn tiền
Khi nhận sai sản phẩm, thiếu phụ kiện hoặc phát hiện vấn đề, vui lòng giữ sản phẩm, bao bì, chứng từ và liên hệ hỗ trợ. Gửi mã đơn, mô tả tình trạng cùng ảnh hoặc video liên quan để cửa hàng kiểm tra. Điều kiện đổi trả, chi phí và thời gian xử lý sẽ được xác nhận theo sản phẩm và nguyên nhân cụ thể. Trường hợp được chấp thuận hoàn tiền, cửa hàng thống nhất phương thức hoàn với khách hàng.

9. Bảo hành và hỗ trợ kỹ thuật
Điều kiện và thời hạn bảo hành theo thông tin của từng sản phẩm hoặc nhà sản xuất. Giữ mã đơn và chứng từ mua hàng để thuận tiện tra cứu. Khi cần hỗ trợ, mô tả lỗi, thời điểm phát sinh và các thao tác đã thử; không tự tháo lắp nếu chưa được hướng dẫn.

10. Liên hệ và phạm vi áp dụng
Hotline hỗ trợ: 0398 716 527. Bạn cũng có thể dùng nút Zalo, Messenger trên website hoặc xem trang Liên hệ. Luôn cung cấp mã đơn khi hỏi về giao hàng, thanh toán, đổi trả hay bảo hành. Website hiện là bản demo phục vụ đồ án; chỉ dùng các phương thức thử nghiệm được hướng dẫn, không chuyển tiền thật theo dữ liệu minh họa. Vui lòng đọc lại chính sách và thông tin đơn trước mỗi lần mua hàng.
TEXT,
        ]);
        $page->created_by ??= $admin->id;
        $page->updated_by = $admin->id;
        $page->save();
    }
}
