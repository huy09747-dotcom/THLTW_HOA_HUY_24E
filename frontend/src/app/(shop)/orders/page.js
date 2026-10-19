"use client";
import Link from "next/link";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import { formatPrice } from "@/utils/format";
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
  return <section className="container catalog-page"><h1>Đơn hàng của tôi</h1><p><Link href="/profile">Về hồ sơ cá nhân</Link></p>
    {pending ? <p role="status">Đang tải đơn hàng…</p> : state.error ? <div><p role="alert" className="error-message">{state.error}</p><button className="primary-button" onClick={() => setRetry(retry+1)}>Thử lại</button></div> : <>
      {state.result.data.length === 0 ? <p>Bạn chưa có đơn hàng. <Link href="/products">Mua sắm ngay</Link></p> : <div style={{overflowX:"auto"}}><table style={{width:"100%",textAlign:"left",borderSpacing:"16px"}}>
        <caption>Lịch sử mua hàng</caption><thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Thanh toán</th><th>Trạng thái</th></tr></thead>
        <tbody>{state.result.data.map(order => <tr key={order.id}><td>{order.code || "#"+order.id}</td><td>{order.created_at ? new Date(order.created_at).toLocaleString("vi-VN") : "—"}</td><td>{formatPrice(order.total)}</td><td>{payments[order.payment_status] || order.payment_status}</td><td>{statuses[order.status] || "—"}</td></tr>)}</tbody>
      </table></div>}
      <div className="auth-links"><button className="primary-button" disabled={page<=1} onClick={() => setPage(page-1)}>Trang trước</button><span>Trang {page} / {state.result.meta.last_page}</span><button className="primary-button" disabled={page>=state.result.meta.last_page} onClick={() => setPage(page+1)}>Trang sau</button></div>
    </>}
  </section>;
}
