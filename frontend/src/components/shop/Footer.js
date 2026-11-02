import ShopLogo from "@/components/ShopLogo";
import Link from "next/link";

export default function Footer({ settings }) {
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Link
            href="/"
            className="footer-brand footer-brand-with-logo"
          >
            <ShopLogo size={44} />
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
          {[['facebook','Facebook'],['instagram','Instagram'],['youtube','YouTube']].map(([key,label]) => settings?.[key] && /^https?:\/\//i.test(settings[key]) ? <a key={key} href={settings[key]} target="_blank" rel="noopener noreferrer">{label}</a> : null)}
        </div>
      </div>
      <div className="container footer-bottom">
        <span>© {new Date().getFullYear()} {settings?.site_name || "HH Shop"}.</span>
        <span>{settings?.footer_text}</span>
      </div>
    </footer>
  );
}
