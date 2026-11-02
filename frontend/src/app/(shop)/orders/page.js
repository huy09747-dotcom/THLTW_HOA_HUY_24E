"use client";
import Link from "next/link";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import { formatPrice } from "@/utils/format";
import styles from "./orders.module.css";
const statuses = {1:"Chờ xác nhận",2:"Đã xác nhận",3:"Đang giao",4:"Hoàn thành",5:"Đã hủy"};
const payments = {unpaid:"Chưa thanh toán",paid:"Đã thanh toán",refunded:"Đã hoàn tiền"};
export default function OrdersPage() {
  const { user, token, loading } = useCart();
  const [page, setPage] = useState(1);
  const [retry, setRetry] = useState(0);
  const [state, setState] = useState({});
  useEffect(() => {
    if (!token) return;
    let active = true;
    api("/my-orders?page=" + page, {token}).then(result => {
      if (active) setState({result, page, token, retry});
    }).catch(error => {
      if (active) setState({error: error.message, page, token, retry});
    });
    return () => { active = false; };
  }, [token, page, retry]);
  if (loading) return <p className="container catalog-page" role="status">Đang kiểm tra tài khoản…</p>;
  if (!user) return <section className="auth-page"><h1>Đơn hàng của tôi</h1><Link href="/login?next=/orders">Đăng nhập để xem đơn hàng</Link></section>;
  const pending = state.page !== page || state.token !== token || state.retry !== retry;
  return <section className={"container "+styles.page}>
    <Link href="/profile" className={styles.back}>← Tài khoản của tôi</Link>
    <header className={styles.heading}><div><p className={styles.eyebrow}>MUA SẮM CÙNG HH SHOP</p><h1>Đơn hàng của tôi</h1><p className={styles.muted}>Theo dõi và xem lại những lựa chọn của bạn.</p></div><Link className={styles.primary} href="/products">Khám phá sản phẩm <span>↗</span></Link></header>
    {pending ? <div className={styles.panel}><p role="status" className={styles.skeleton}>Đang tải đơn hàng…</p></div> : state.error ? <div className={styles.error}><p role="alert">{state.error}</p><button className={styles.button} onClick={() => setRetry(retry+1)}>Thử lại</button></div> : <>
      <div className={styles.heading}><h2>Lịch sử mua hàng <span className={styles.count}>{state.result.meta.total}</span></h2><span className={styles.muted}>Mới nhất trước</span></div>
      {state.result.data.length===0 ? <div className={styles.panel}><div className={styles.empty}><span className={styles.icon} aria-hidden="true">◇</span><h2>Chưa có đơn hàng nào</h2><p className={styles.muted}>Sản phẩm yêu thích tiếp theo đang chờ bạn khám phá.</p><Link href="/products" className={styles.primary}>Bắt đầu mua sắm →</Link></div></div> : state.result.data.map(order => <article key={order.id} className={styles.card}><div className={styles.top}><div><span className={styles.label}>Mã đơn hàng</span><p className={styles.code}>{"HH-" + String(order.id).padStart(6, "0")}</p></div><span className={styles.badge} data-status={order.status}>{statuses[order.status] || "Chưa xác định"}</span></div><div className={styles.body}><div className={styles.row}><span className={styles.icon} aria-hidden="true">◇</span><div><span className={styles.label}>Ngày đặt hàng</span><p>{order.created_at ? new Date(order.created_at).toLocaleString("vi-VN") : "—"}</p><p className={styles.muted}>{payments[order.payment_status] || order.payment_status}</p></div></div><div><span className={styles.label}>Tổng thanh toán</span><strong className={styles.price}>{formatPrice(order.total)}</strong></div><Link href={"/orders/"+order.id} className={styles.primary} aria-label={"Xem chi tiết đơn "+(order.code || order.id)}>Xem chi tiết <span>→</span></Link></div></article>)}
      {state.result.meta.last_page>1 && <nav className={styles.pagination} aria-label="Phân trang đơn hàng"><button className={styles.button} disabled={page<=1} onClick={() => setPage(page-1)}>← Trang trước</button><span className={styles.muted}>{page} / {state.result.meta.last_page}</span><button className={styles.button} disabled={page>=state.result.meta.last_page} onClick={() => setPage(page+1)}>Trang sau →</button></nav>}
    </>}
  </section>;
}
