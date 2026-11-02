"use client";

import Link from "next/link";
import { useParams } from "next/navigation";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import OrderPayment from "@/components/shop/OrderPayment";
import { formatPrice } from "@/utils/format";
import styles from "../orders.module.css";

const statuses = {
  1: "Chờ xác nhận",
  2: "Đã xác nhận",
  3: "Đang giao",
  4: "Hoàn thành",
  5: "Đã hủy",
};
const payments = {
  unpaid: "Chưa thanh toán",
  paid: "Đã thanh toán",
  refunded: "Đã hoàn tiền",
};

export default function OrderDetailPage() {
  const { id } = useParams();
  const { user, token, loading } = useCart();
  const [state, setState] = useState({});
  const [retry, setRetry] = useState(0);
  const [cancelling, setCancelling] = useState(false);
  const [cancelError, setCancelError] = useState("");

  async function cancelOrder() {
    if (!window.confirm("Bạn có chắc muốn hủy đơn hàng này?")) return;
    setCancelling(true);
    setCancelError("");
    try {
      await api(`/my-orders/${encodeURIComponent(id)}/cancel`, { method: "POST", token });
      setRetry(n => n + 1);
    } catch (error) {
      setCancelError(error.message);
    } finally {
      setCancelling(false);
    }
  }

  useEffect(() => {
    if (!token) return;
    let active = true;

    api("/my-orders/" + encodeURIComponent(id), { token })
      .then(result => {
        if (active) setState({ order: result.data, token, id, retry });
      })
      .catch(error => {
        if (active) {
          setState({
            error: error.status === 404
              ? "Không tìm thấy đơn hàng của bạn."
              : error.message,
            token,
            id,
            retry,
          });
        }
      });

    return () => { active = false; };
  }, [id, token, retry]);

  if (loading) {
    return <p className="container catalog-page" role="status">Đang kiểm tra tài khoản…</p>;
  }

  if (!user) {
    return (
      <section className="auth-page">
        <h1>Chi tiết đơn hàng</h1>
        <Link href={"/login?next=" + encodeURIComponent("/orders/" + id)}>
          Đăng nhập để xem đơn hàng
        </Link>
      </section>
    );
  }

  const pending = state.token !== token || state.id !== id || state.retry !== retry;
  const order = state.order;

  return (
    <section className={"container " + styles.page}>
      <Link href="/orders" className={styles.button}>← Quay lại đơn hàng của tôi</Link>
      <header className={styles.heading}>
        <div>
          <p className={styles.eyebrow}>HH SHOP / ĐƠN HÀNG</p>
          <h1>Chi tiết đơn hàng</h1>
        </div>
        <Link className={styles.button} href="/contact">Liên hệ hỗ trợ ↗</Link>
      </header>

      {pending ? (
        <div className={styles.panel}>
          <p className={styles.skeleton} role="status">Đang tải đơn hàng…</p>
        </div>
      ) : state.error ? (
        <div className={styles.error}>
          <p role="alert">{state.error}</p>
          <button className={styles.button} onClick={() => setRetry(n => n + 1)}>
            Thử lại
          </button>
        </div>
      ) : (
        <div className={styles.grid}>
          <div className={styles.stack}>
            <section className={styles.panel}>
              <div className={styles.top}>
                <div>
                  <span className={styles.label}>Mã đơn hàng</span>
                  <p className={styles.code}>{"HH-" + String(order.id).padStart(6, "0")}</p>
                  <p className={styles.muted}>
                    {order.created_at ? new Date(order.created_at).toLocaleString("vi-VN") : "—"}
                  </p>
                </div>
                <span className={styles.badge} data-status={order.status}>
                  {statuses[order.status] || "Chưa xác định"}
                </span>
              </div>
              {Number(order.status) === 5 ? (
                <p className={styles.cancel}>Đơn hàng đã hủy.</p>
              ) : (
                <ol className={styles.progress} aria-label="Tiến trình đơn hàng">
                  {[1, 2, 3, 4].map(step => (
                    <li
                      key={step}
                      className={styles.step}
                      data-done={Number(order.status) >= step}
                      aria-current={Number(order.status) === step ? "step" : undefined}
                    >
                      <span>{Number(order.status) > step ? "✓" : step}</span>
                      {statuses[step]}
                    </li>
                  ))}
                </ol>
              )}
            </section>

            <section className={styles.panel}>
              <div className={styles.top}>
                <h2>
                  Sản phẩm đã đặt
                  <span className={styles.count}>
                    {order.items.reduce((sum, item) => sum + Number(item.qty), 0)}
                  </span>
                </h2>
              </div>
              {order.items.map(item => (
                <article className={styles.product} key={item.id}>
                  <span className={styles.icon} aria-hidden="true">◇</span>
                  <div>
                    <strong>{item.item_name || "Sản phẩm #" + item.product_id}</strong>
                    <p className={styles.muted}>{formatPrice(item.price)} × {item.qty}</p>
                  </div>
                  <strong>{formatPrice(item.amount)}</strong>
                </article>
              ))}
            </section>

            <section className={styles.panel}>
              <div className={styles.section}>
                <h2>Thông tin nhận hàng</h2>
                <dl className={styles.recipient}>
                  <div>
                    <dt>Người nhận</dt>
                    <dd>{order.name}</dd>
                  </div>
                  <div>
                    <dt>Số điện thoại</dt>
                    <dd>{order.phone}</dd>
                  </div>
                  {order.email && (
                    <div className={styles.fullWidth}>
                      <dt>Email</dt>
                      <dd>{order.email}</dd>
                    </div>
                  )}
                  <div className={styles.fullWidth}>
                    <dt>Địa chỉ nhận hàng</dt>
                    <dd>{order.address}</dd>
                  </div>
                  {order.note && (
                    <div className={styles.fullWidth}>
                      <dt>Ghi chú</dt>
                      <dd>{order.note}</dd>
                    </div>
                  )}
                </dl>
              </div>
            </section>
          </div>
          <aside className={styles.stack}>
            <section className={styles.summary}>
              <h2>Thông tin thanh toán</h2>
              <div className={styles.paymentInfo}>
                <span className={styles.label}>Phương thức</span>
                <strong>
                  {({
                    cod: "Thanh toán khi nhận hàng",
                    payos: "Chuyển khoản ngân hàng",
                    momo: "MoMo sandbox",
                    paypal: "PayPal sandbox",
                    vnpay: "Thẻ nội địa / VNPAY sandbox",
                  })[order.payment_method] || order.payment_method || "—"}
                </strong>
                <span className={styles.paymentStatus} data-status={order.payment_status}>
                  {order.payment_status === "paid" && <span aria-hidden="true">✓ </span>}
                  {payments[order.payment_status] || order.payment_status || "—"}
                </span>
              </div>
              <div className={styles.line}>
                <span>Tạm tính</span>
                <span>{formatPrice(order.subtotal)}</span>
              </div>
              <div className={styles.line}>
                <span>Giảm giá</span>
                <span>− {formatPrice(order.discount || 0)}</span>
              </div>
              <div className={styles.total}>
                <span className={styles.label}>Tổng thanh toán</span>
                <strong>{formatPrice(order.total)}</strong>
              </div>
              {Number(order.status) === 1 && order.payment_status === "unpaid" && (
            <button
              type="button"
              className={styles.cancelButton}
              disabled={cancelling}
              onClick={cancelOrder}
            >
              {cancelling ? "Đang hủy…" : "Hủy đơn hàng"}
            </button>
          )}
          {cancelError && <p role="alert" className="error-message">{cancelError}</p>}
          <OrderPayment order={order} onUpdated={() => setRetry(n => n + 1)} />
            </section>
          </aside>
        </div>
      )}
      <div className={styles.foot}>
        <Link href="/orders" className={styles.button}>← Danh sách đơn hàng</Link>
        <Link href="/products" className={styles.button}>Tiếp tục mua sắm →</Link>
      </div>
    </section>
  );
}
