"use client";
import Link from "next/link";
import { useParams } from "next/navigation";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import { formatPrice } from "@/utils/format";

const labels={1:"Chờ xác nhận",2:"Đã xác nhận",3:"Đang giao",4:"Hoàn thành",5:"Đã hủy"};
export default function GuestOrder() {
  const {id}=useParams(); const {guestToken,loading}=useCart();
  const [state,setState]=useState({}); const [busy,setBusy]=useState(false); const [revision,setRevision]=useState(0);
  useEffect(()=>{
    if(!guestToken)return;
    let active=true;
    api(`/guest/orders/${id}`,{guestToken}).then(r=>{if(active)setState({order:r.data});}).catch(e=>{if(active)setState({error:e.message});});
    return()=>{active=false;};
  },[id,guestToken,revision]);
  async function pay(verify=false) {
    setBusy(true);
    try {
      const r=await api(`/guest/orders/${id}/${verify?'verify':'payment'}`,{guestToken,method:'POST',body:verify?undefined:{return_origin:window.location.origin}});
      if(verify) {setRevision(n=>n+1);setState(s=>({...s,notice:r.data.payment_status==='paid'?'Đã thanh toán.':'Chưa ghi nhận thanh toán.'}));}
      else window.location.assign(r.data.url);
    } catch(e){setState(s=>({...s,error:e.message}));} finally{setBusy(false);}
  }
  const order=state.order;
  return <section className="container catalog-page">
    <Link href="/cart" className="secondary-button">← Quay lại giỏ hàng</Link><h1>Đơn hàng của khách</h1>
    <p>Đơn được lưu trong phiên trình duyệt đã đặt hàng. Giữ lại email và mã đơn để liên hệ khi cần hỗ trợ.</p>
    {state.error&&<p role="alert" className="error-message">{state.error}</p>}
    {loading?<p>Đang tải…</p>:!guestToken?<p>Mở trên trình duyệt đã đặt hàng để xem đơn. <Link href="/contact">Liên hệ hỗ trợ</Link></p>:!order?<p>Đang tải thông tin đơn…</p>:<div className="feature-panel">
      <h2>HH-{String(order.id).padStart(6,'0')} · {labels[order.status]}</h2>
      <p>{order.name} · {order.phone}</p><p>{order.address}</p>
      {order.items.map(item=><div className="dashboard-status" key={item.id}><span>{item.item_name} × {item.qty}</span><strong>{formatPrice(item.amount)}</strong></div>)}
      <p>Tạm tính: {formatPrice(order.subtotal)}</p><p>Giảm giá: − {formatPrice(order.discount)}</p><h2>Tổng tiền: {formatPrice(order.total)}</h2>
      <p>{order.payment_method==='cod'?'Thanh toán khi nhận hàng':'Thanh toán chuyển khoản'} · {order.payment_status==='paid'?'Đã thanh toán':'Chưa thanh toán'}</p>
      {order.payment_method==='payos'&&order.payment_status==='unpaid'&&Number(order.status)!==5&&<div className="feature-actions"><button disabled={busy} onClick={()=>pay()}>Thanh toán ngay</button><button disabled={busy} onClick={()=>pay(true)}>Kiểm tra thanh toán</button></div>}
      {state.notice&&<p role="status">{state.notice}</p>}
    </div>}
  </section>;
}
