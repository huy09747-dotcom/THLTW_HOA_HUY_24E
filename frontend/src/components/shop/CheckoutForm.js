"use client";

import CouponEntry from "./CouponEntry";
import { useEffect, useState } from "react";
import { api } from "@/services/api";
import { useCart } from "./CartProvider";

export default function CheckoutForm({ onSuccess }) {
  const { user, busy, checkout } = useCart();
  const [method, setMethod] = useState("cod");
  const [submitting, setSubmitting] = useState(false);
  const [open, setOpen] = useState(false);
  const [methods,setMethods]=useState([{id:'cod',label:'Thanh toán khi nhận hàng',enabled:true}]);
  const [methodError,setMethodError]=useState('');
  useEffect(()=>{let active=true;api('/payment-methods').then(r=>{if(active)setMethods(r.data);}).catch(e=>{if(active)setMethodError(e.message);});return()=>{active=false;};},[]);

  async function submit(event) {
    event.preventDefault();
    if (submitting || busy) return;
    const data = Object.fromEntries(new FormData(event.currentTarget));
    setSubmitting(true);
    try {
      const order = await checkout(data);
      if (order) await onSuccess(order);
    } finally {
      setSubmitting(false);
    }
  }

  if (!open) {
    return (
      <button
        type="button"
        className="primary-button checkout-button"
        disabled={busy || submitting}
        onClick={() => setOpen(true)}
      >
        Tiến hành đặt hàng
      </button>
    );
  }

  return (
    <form className="checkout-form cart-checkout-form" onSubmit={submit}>
      <h2>Thông tin nhận hàng</h2>
      <div className="checkout-field">
        <label htmlFor="recipient-name">Họ tên người nhận</label>
        <input
          id="recipient-name"
          name="name"
          autoComplete="name"
          defaultValue={user.name || ""}
          maxLength={255}
          required
        />
      </div>
      <div className="checkout-field">
        <label htmlFor="recipient-phone">Số điện thoại</label>
        <input
          id="recipient-phone"
          name="phone"
          type="tel"
          autoComplete="tel"
          defaultValue={user.phone || ""}
          pattern="[+]?[0-9]{9,15}"
          title="Nhập 9 đến 15 chữ số, có thể bắt đầu bằng dấu +"
          required
        />
      </div>
      <div className="checkout-field">
        <label htmlFor="recipient-email">Email</label>
        <input
          id="recipient-email"
          name="email"
          type="email"
          autoComplete="email"
          defaultValue={user.email || ""}
          maxLength={255}
          required
        />
      </div>
      <div className="checkout-field">
        <label htmlFor="recipient-address">Địa chỉ nhận hàng</label>
        <textarea
          id="recipient-address"
          name="address"
          autoComplete="street-address"
          defaultValue={user.address || ""}
          maxLength={255}
          rows={2}
          required
        />
      </div>
      <div className="checkout-field">
        <label htmlFor="order-note">Ghi chú</label>
        <textarea id="order-note" name="note" maxLength={2000} rows={1} />
      </div>
      <CouponEntry />
      <label htmlFor="payment-method">Phương thức thanh toán</label>
      <select id="payment-method" name="payment_method" value={method} onChange={(event) => setMethod(event.target.value)} disabled={busy || submitting}>{methods.map(m=><option key={m.id} value={m.id} disabled={!m.enabled}>{m.label}{!m.enabled?' — chưa cấu hình':''}</option>)}</select>
      {methodError&&<p role="alert">{methodError}</p>}
      <button
        type="submit"
        className="primary-button checkout-button"
        disabled={busy || submitting}
      >
        {busy || submitting ? "Đang xử lý…" : method === "payos" ? "Thanh toán ngay" : "Xác nhận đặt hàng"}
      </button>
    </form>
  );
}
