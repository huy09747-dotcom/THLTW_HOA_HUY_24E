"use client";

import Link from "next/link";
import { api } from "@/services/api";
import { useState } from "react";
import CheckoutForm from "@/components/shop/CheckoutForm";

import { useCart } from "@/components/shop/CartProvider";
import { ProductImage } from "@/components/shop/ProductCard";
import { formatPrice } from "@/utils/format";

export default function CartPage() {
  const [order, setOrder] = useState(null);
  const { user, token, cart, loading, busy, error, logout, changeCart } = useCart();

  const [paymentError, setPaymentError] = useState("");
  const [openingPayment, setOpeningPayment] = useState(false);

  async function completeCheckout(savedOrder) {
    setOrder(savedOrder);
    if (savedOrder.payment_method !== "payos") return;
    setOpeningPayment(true);
    setPaymentError("");
    try {
      const result = await api(`/my-orders/${savedOrder.id}/payment`, {
        method: "POST",
        token,
        body: { return_origin: window.location.origin },
      });
      window.location.assign(result.data.url);
    } catch (exception) {
      setPaymentError(exception.message);
    } finally {
      setOpeningPayment(false);
    }
  }

  if (loading)
    return (
      <div
        className="container catalog-page cart-page"
        role="status"
      >
        Đang tải giỏ hàng…
      </div>
    );
  return (
    <div className="container catalog-page cart-page">
      <p className="breadcrumb">
        <Link href="/">Trang chủ</Link> / Giỏ hàng
      </p>
      <h1>Giỏ hàng của bạn</h1>
      {user && order && (
        <div className="success" role="status">
          <h2>{openingPayment ? "Đang mở thanh toán…" : "Đã lưu đơn hàng"}</h2>
          <p>Mã đơn hàng: {order.code}</p>
          <p>Tổng tiền: {formatPrice(order.total)}</p>
          <p>{order.payment_method==='cod'?'Thanh toán khi nhận hàng.':'Mở chi tiết đơn hàng để tiếp tục thanh toán.'}</p><p>{order.email_status==='queued'?'Email xác nhận đã được đưa vào hàng đợi gửi.':'Đơn đã được lưu nhưng chưa đưa được email vào hàng đợi. Vui lòng liên hệ hỗ trợ.'}</p>
          <Link href={`/orders/${order.id}`} className="primary-button">Xem chi tiết đơn hàng</Link>
        </div>
      )}
      {paymentError && <p role="alert" className="error-message">{paymentError} Bạn có thể mở chi tiết đơn để thanh toán lại.</p>}
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
            <Link href="/orders">Đơn hàng của tôi</Link>
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
                <CheckoutForm onSuccess={completeCheckout} />
                <Link href="/products">← Tiếp tục mua sắm</Link>
              </aside>
            </div>
          )}
        </>
      )}
    </div>
  );
}
