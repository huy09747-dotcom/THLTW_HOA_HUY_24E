"use client";
import Link from "next/link";
import { useParams } from "next/navigation";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import OrderPayment from "@/components/shop/OrderPayment";
import { formatPrice } from "@/utils/format";
import styles from "../orders.module.css";
const statuses={1:"Chờ xác nhận",2:"Đã xác nhận",3:"Đang giao",4:"Hoàn thành",5:"Đã hủy"};
const payments={unpaid:"Chưa thanh toán",paid:"Đã thanh toán",refunded:"Đã hoàn tiền"};
export default function OrderDetailPage() {
  const {id}=useParams();
  const {user,token,loading}=useCart();
  const [state,setState]=useState({});
  const [retry,setRetry]=useState(0);
  useEffect(() => {
    if(!token)return;
    let active=true;
    api("/my-orders/"+encodeURIComponent(id),{token}).then(result => {
      if(active)setState({order:result.data,token,id,retry});
    }).catch(error => {
      if(active)setState({error:error.status===404 ? "Không tìm thấy đơn hàng của bạn." : error.message,token,id,retry});
    });
    return () => {active=false;};
  },[id,token,retry]);
  if(loading)return <p className="container catalog-page" role="status">Đang kiểm tra tài khoản…</p>;
  if(!user)return <section className="auth-page"><h1>Chi tiết đơn hàng</h1><Link href={"/login?next="+encodeURIComponent("/orders/"+id)}>Đăng nhập để xem đơn hàng</Link></section>;
  const pending=state.token!==token || state.id!==id || state.retry!==retry;
  const order=state.order;
  return <section className={"container "+styles.page}>
    <Link href="/orders" className={styles.back}>← Quay lại đơn hàng của tôi</Link>
    <header className={styles.heading}><div><p className={styles.eyebrow}>HH SHOP / ĐƠN HÀNG</p><h1>Chi tiết đơn hàng</h1><p className={styles.muted}>Thông tin mua hàng và hành trình đơn hàng của bạn.</p></div><Link className={styles.button} href="/contact">Liên hệ hỗ trợ ↗</Link></header>
    {pending ? <div className={styles.panel}><p className={styles.skeleton} role="status">Đang tải đơn hàng…</p></div> : state.error ? <div className={styles.error}><p role="alert">{state.error}</p><button className={styles.button} onClick={() => setRetry(retry+1)}>Thử lại</button></div> : <div className={styles.grid}>
      <div className={styles.stack}>
        <section className={styles.panel}><div className={styles.top}><div><span className={styles.label}>Mã đơn hàng</span><p className={styles.code}>{order.code || "#"+order.id}</p><p className={styles.muted}>{order.created_at ? new Date(order.created_at).toLocaleString("vi-VN") : "—"}</p></div><span className={styles.badge} data-status={order.status}>{statuses[order.status] || "Chưa xác định"}</span></div>
          {Number(order.status)===5 ? <p className={styles.cancel}>Đơn hàng đã hủy. Liên hệ cửa hàng nếu bạn cần hỗ trợ thêm.</p> : <ol className={styles.progress} aria-label="Tiến trình đơn hàng">{[1,2,3,4].map(step => <li key={step} className={styles.step} data-done={Number(order.status)>=step} aria-current={Number(order.status)===step ? "step" : undefined}><span>{Number(order.status)>step ? "✓" : step}</span>{statuses[step]}</li>)}</ol>}
        </section>
        <section className={styles.panel}><div className={styles.top}><h2>Sản phẩm đã đặt <span className={styles.count}>{order.items.reduce((sum,item)=>sum+Number(item.qty),0)}</span></h2></div>{order.items.map(item => <article className={styles.product} key={item.id}><span className={styles.icon} aria-hidden="true">◇</span><div><strong>{item.item_name || "Sản phẩm #"+item.product_id}</strong><p className={styles.muted}>{formatPrice(item.price)} × {item.qty}</p></div><strong>{formatPrice(item.amount)}</strong></article>)}</section>
        <section className={styles.panel}><div className={styles.section}><div className={styles.row}><span className={styles.icon} aria-hidden="true">⌖</span><h2>Thông tin nhận hàng</h2></div><div className={styles.address}><h3>{order.name}</h3><p>{order.phone} · {order.email || "Chưa có email"}</p><p>{order.address}</p>{order.note && <p className={styles.note}><span className={styles.label}>Ghi chú</span>{order.note}</p>}</div></div></section>
      </div>
      <aside className={styles.stack}><OrderPayment order={order} onUpdated={()=>setRetry(n=>n+1)} /><section className={styles.summary}><h2>Tóm tắt thanh toán</h2><div className={styles.line}><span>Tạm tính</span><span>{formatPrice(order.subtotal)}</span></div><div className={styles.line}><span>Giảm giá</span><span>− {formatPrice(order.discount || 0)}</span></div><div className={styles.total}><span className={styles.label}>Tổng thanh toán</span><strong>{formatPrice(order.total)}</strong></div></section><section className={styles.panel}><div className={styles.section}><h2>Phương thức thanh toán</h2><h3>{order.payment_method==="cod" ? "Thanh toán khi nhận hàng" : ({payos:"Chuyển khoản ngân hàng",momo:"MoMo sandbox",paypal:"PayPal sandbox",vnpay:"Thẻ nội địa / VNPAY sandbox"}[order.payment_method] || order.payment_method || "—")}</h3><p className={styles.muted}>{payments[order.payment_status] || order.payment_status || "—"}</p></div></section><p className={styles.muted}>Cần trợ giúp với đơn hàng? Khi liên hệ, hãy cung cấp mã đơn để được hỗ trợ nhanh hơn.</p></aside>
    </div>}
    <div className={styles.foot}><Link href="/orders">← Danh sách đơn hàng</Link><Link href="/products">Tiếp tục mua sắm →</Link></div>
  </section>;
}
