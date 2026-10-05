"use client";

import { useState } from "react";
import { useCart } from "./CartProvider";

export default function CheckoutForm({ onSuccess }) {
  const { user, busy, checkout } = useCart();
  const [open, setOpen] = useState(false);

  async function submit(event) {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget));
    const order = await checkout(data);
    if (order) onSuccess(order);
  }

  if (!open) {
    return (
      <button
        type="button"
        className="primary-button checkout-button"
        disabled={busy}
        onClick={() => setOpen(true)}
      >
        Tiến hành đặt hàng
      </button>
    );
  }

  return (
    <form className="checkout-form" onSubmit={submit}>
      <h2>Thông tin nhận hàng</h2>
      <label htmlFor="recipient-name">Họ tên người nhận</label>
      <input
        id="recipient-name"
        name="name"
        autoComplete="name"
        defaultValue={user.name || ""}
        maxLength={255}
        required
      />
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
      <label htmlFor="recipient-address">Địa chỉ nhận hàng</label>
      <textarea
        id="recipient-address"
        name="address"
        autoComplete="street-address"
        defaultValue={user.address || ""}
        maxLength={255}
        rows={3}
        required
      />
      <label htmlFor="order-note">Ghi chú</label>
      <textarea id="order-note" name="note" maxLength={2000} rows={2} />
      <p>Thanh toán khi nhận hàng (COD)</p>
      <button
        type="submit"
        className="primary-button checkout-button"
        disabled={busy}
      >
        {busy ? "Đang xử lý…" : "Xác nhận đặt hàng"}
      </button>
    </form>
  );
}
