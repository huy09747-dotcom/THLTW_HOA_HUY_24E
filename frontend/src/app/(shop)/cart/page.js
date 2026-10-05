"use client";

import Link from "next/link";
import { useState } from "react";
import CheckoutForm from "@/components/shop/CheckoutForm";

import { useCart } from "@/components/shop/CartProvider";
import { ProductImage } from "@/components/shop/ProductCard";
import { formatPrice } from "@/utils/format";

export default function CartPage() {
  const [order, setOrder] = useState(null);
  const { user, cart, loading, busy, error, logout, changeCart } = useCart();

  if (loading)
    return (
      <div
        className="container catalog-page"
        role="status"
      >
        Đang tải giỏ hàng…
      </div>
    );
  return (
    <div className="container catalog-page">
      <p className="breadcrumb">
        <Link href="/">Trang chủ</Link> / Giỏ hàng
      </p>
      <h1>Giỏ hàng của bạn</h1>
      {user && order && (
        <div className="success" role="status">
          <h2>Đặt hàng thành công</h2>
          <p>Mã đơn hàng: {order.code}</p>
          <p>Tổng tiền: {formatPrice(order.total)}</p>
          <p>Thanh toán khi nhận hàng.</p>
        </div>
      )}
      {error && (
        <p
          role="alert"
          className="error-message"
        >
          {error}
        </p>
      )}
      {!user ? (
        <div className="login-panel">
          <h2>Đăng nhập để sử dụng giỏ hàng</h2>
          <p>Giỏ hàng được lưu theo tài khoản của bạn.</p>
          <Link
            href="/login?next=/cart"
            className="primary-button"
          >
            Đăng nhập
          </Link>
        </div>
      ) : (
        <>
          <div className="account-row">
            <span>Xin chào, {user?.name || "bạn"}</span>
            {user && (
              <button
                type="button"
                disabled={busy}
                onClick={logout}
              >
                Đăng xuất
              </button>
            )}
          </div>
          {!cart.data.length ? (
            <div className="empty-cart">
              <span aria-hidden="true">◇</span>
              <h2>Giỏ hàng đang trống</h2>
              <p>Khám phá và chọn sản phẩm phù hợp với bạn.</p>
              <Link
                href="/products"
                className="primary-button"
              >
                Xem sản phẩm
              </Link>
            </div>
          ) : (
            <div className="cart-layout">
              <div className="cart-items">
                {cart.data.map((item) => (
                  <article
                    key={item.id}
                    className="cart-item"
                  >
                    <ProductImage
                      image={item.image}
                      name={item.name}
                    />
                    <div className="cart-item-info">
                      <Link href={item.slug ? `/products/${encodeURIComponent(item.slug)}` : "/products"}>
                        <h2>{item.name}</h2>
                      </Link>
                      <p>{formatPrice(item.price)}</p>
                      {!item.available && (
                        <p className="error-message">
                          Sản phẩm không còn khả dụng hoặc không đủ tồn kho.
                        </p>
                      )}
                      <div className="quantity-control">
                        <button
                          disabled={busy || item.quantity <= 1}
                          aria-label={`Giảm số lượng ${item.name}`}
                          onClick={() =>
                            changeCart(`/cart/items/${item.id}`, "PUT", {
                              quantity: item.quantity - 1,
                            })
                          }
                        >
                          −
                        </button>
                        <span aria-label="Số lượng">{item.quantity}</span>
                        <button
                          disabled={busy || item.quantity >= Math.min(item.stock, 999)}
                          aria-label={`Tăng số lượng ${item.name}`}
                          onClick={() =>
                            changeCart(`/cart/items/${item.id}`, "PUT", {
                              quantity: item.quantity + 1,
                            })
                          }
                        >
                          +
                        </button>
                        <button
                          className="remove-item"
                          disabled={busy}
                          onClick={() => changeCart(`/cart/items/${item.id}`, "DELETE")}
                        >
                          Xóa
                        </button>
                      </div>
                    </div>
                    <strong>{item.available ? formatPrice(item.subtotal) : "—"}</strong>
                  </article>
                ))}
              </div>
              <aside className="cart-summary">
                <h2>Tóm tắt giỏ hàng</h2>
                <p>{cart.count} sản phẩm</p>
                <div>
                  <span>Tạm tính</span>
                  <strong>{formatPrice(cart.total)}</strong>
                </div>
                <CheckoutForm onSuccess={setOrder} />
                <Link href="/products">← Tiếp tục mua sắm</Link>
              </aside>
            </div>
          )}
        </>
      )}
    </div>
  );
}
