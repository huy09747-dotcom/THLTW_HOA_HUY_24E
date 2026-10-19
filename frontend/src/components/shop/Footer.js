import Link from "next/link";

export default function Footer({ settings }) {
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Link
            href="/"
            className="footer-brand"
          >
            {settings?.site_name || "HH Shop"}
          </Link>
          <p>
            {settings?.tagline}
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
          <Link href="/about">Giới thiệu</Link>
          <Link href="/purchase-policy">Chính sách mua hàng</Link>
          <Link href="/contact">Liên hệ</Link>
          {settings?.contact_email && <p>{settings.contact_email}</p>}
          {settings?.contact_phone && <p>{settings.contact_phone}</p>}
          {settings?.address && <p>{settings.address}</p>}
        </div>
      </div>
      <div className="container footer-bottom">
        <span>© {new Date().getFullYear()} {settings?.site_name || "HH Shop"}.</span>
        <span>{settings?.footer_text}</span>
      </div>
    </footer>
  );
}
