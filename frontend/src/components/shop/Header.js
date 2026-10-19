"use client";

import Link from "next/link";
import CategoryMenu from "./CategoryMenu";
import { usePathname } from "next/navigation";
import { useCart } from "@/components/shop/CartProvider";

export default function Header({ settings }) {
  const pathname = usePathname();
  const { user, cart, loading } = useCart();
  return (
    <header className="site-header">
      <div className="container header-top">
        <Link
          href="/"
          className="brand"
          aria-label={(settings?.site_name || "HH Shop") + " - Trang chủ"}
        >
          {settings?.logo && /^https?:\/\//i.test(settings.logo) ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={settings.logo} alt="" width={44} height={44} style={{objectFit:"contain"}} />
          ) : <span className="brand-mark">HH</span>}
          <span>
            {settings?.site_name || "HH Shop"}
            <small>{settings?.tagline}</small>
          </span>
        </Link>
        <form
          className="search-box"
          role="search"
          action="/products"
        >
          <span aria-hidden="true">⌕</span>
          <input
            name="q"
            aria-label="Tìm kiếm sản phẩm"
            placeholder="Bạn đang tìm sản phẩm gì?"
          />
          <button type="submit">Tìm</button>
        </form>
        <div className="header-actions">
          <Link href={user ? "/profile" : "/login"}>
            {loading ? "Tài khoản" : user ? "Tài khoản" : "Đăng nhập"}
          </Link>
          {user && <Link href="/orders">Đơn hàng</Link>}
          <Link href="/cart">
            Giỏ hàng <span>{cart.count}</span>
          </Link>
        </div>
      </div>
      <nav
        className="main-nav"
        aria-label="Điều hướng chính"
      >
        <div className="container nav-inner">
          <Link
            href="/"
            className={pathname === "/" ? "nav-active" : ""}
          >
            Trang chủ
          </Link>
          <Link
            href="/products"
            className={pathname.startsWith("/products") ? "nav-active" : ""}
          >
            Sản phẩm
          </Link>
          <CategoryMenu />
          <Link href="/about">Giới thiệu</Link>
          <Link href="/contact">Liên hệ</Link>
          <span className="nav-note">Khám phá không gian công nghệ của bạn</span>
        </div>
      </nav>
    </header>
  );
}
