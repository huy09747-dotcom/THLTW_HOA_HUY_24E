"use client";

import Link from "next/link";
import CategoryMenu from "./CategoryMenu";
import { usePathname } from "next/navigation";
import { useCart } from "@/components/shop/CartProvider";

export default function Header() {
  const pathname = usePathname();
  const { user, cart, loading } = useCart();
  return (
    <header className="site-header">
      <div className="container header-top">
        <Link
          href="/"
          className="brand"
          aria-label="HH Shop - Trang chủ"
        >
          <span className="brand-mark">HH</span>
          <span>
            HH<span className="brand-light"> Shop</span>
            <small>CÔNG NGHỆ MỖI NGÀY</small>
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
          <Link href="/login">
            {loading ? "Tài khoản" : user ? "Tài khoản" : "Đăng nhập"}
          </Link>
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
          <span className="nav-note">Khám phá không gian công nghệ của bạn</span>
        </div>
      </nav>
    </header>
  );
}
