"use client";

import Link from "next/link";
import ShopIcon from "./ShopIcon";
import CategoryMenu from "./CategoryMenu";
import { usePathname } from "next/navigation";
import { useCart } from "@/components/shop/CartProvider";

export default function Header({ settings, menus }) {
  const pathname = usePathname();
  const { user, cart, loading } = useCart();
  const accountLabel = loading ? "Tài khoản" : user ? `Tài khoản: ${user.name || user.username || "của bạn"}` : "Đăng nhập";

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
          <input
            name="q"
            aria-label="Tìm kiếm sản phẩm"
            placeholder="Bạn đang tìm sản phẩm gì?"
          />
          <button type="submit" className="search-icon-button" title="Tìm kiếm" aria-label="Tìm kiếm sản phẩm">
            <ShopIcon name="search" />
          </button>
        </form>
        <div className="header-actions">
          <Link
            href={user ? "/profile" : "/login"}
            className="shop-icon-button"
            title={accountLabel}
            aria-label={accountLabel}
          >
            <ShopIcon name="user" />
          </Link>
          {user && (
            <Link href="/orders" className="shop-icon-button" title="Đơn hàng" aria-label="Đơn hàng của bạn">
              <ShopIcon name="orders" />
            </Link>
          )}
          <Link
            href="/cart"
            className="shop-icon-button"
            title="Giỏ hàng"
            aria-label={`Giỏ hàng: ${cart.count} sản phẩm`}
          >
            <ShopIcon name="cart" />
            <span className="cart-count" aria-hidden="true">{cart.count}</span>
          </Link>
        </div>
      </div>
      <nav
        className="main-nav"
        aria-label="Điều hướng chính"
      >
        <div className="container nav-inner">
          {menus ? menus.map(item=>item.link==='/#danh-muc'?<CategoryMenu key={item.id} label={item.name}/>:<Link key={item.id} href={item.link} className={pathname===item.link?'nav-active':''}>{item.name}</Link>) : <>
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
          <Link href="/news" className={pathname === "/news" || pathname.startsWith("/news/") ? "nav-active" : ""}>Bài viết</Link>
          <Link href="/about">Giới thiệu</Link>
          <Link href="/contact">Liên hệ</Link>
          </>}
          <span className="nav-note">Khám phá không gian công nghệ của bạn</span>
        </div>
      </nav>
    </header>
  );
}
