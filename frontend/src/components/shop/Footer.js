import Link from "next/link";

export default function Footer() {
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Link
            href="/"
            className="footer-brand"
          >
            HH<span> Shop</span>
          </Link>
          <p>
            Không gian khám phá sản phẩm công nghệ
            <br />
            cho cuộc sống mỗi ngày.
          </p>
        </div>
        <div>
          <h2>Khám phá</h2>
          <Link href="/">Trang chủ</Link>
          <Link href="/products">Sản phẩm</Link>
          <Link href="/#danh-muc">Danh mục</Link>
        </div>
        <div>
          <h2>Thông tin website</h2>
          <p>Đồ án Thực hành lập trình web</p>
          <p>THLTW_HOA_HUY_24E</p>
        </div>
      </div>
      <div className="container footer-bottom">
        <span>© {new Date().getFullYear()} HH Shop.</span>
        <span>Công nghệ gần hơn với bạn.</span>
      </div>
    </footer>
  );
}
