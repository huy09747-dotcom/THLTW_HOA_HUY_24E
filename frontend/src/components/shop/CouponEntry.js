"use client";

import { useState } from "react";
import { api } from "@/services/api";
import { useCart } from "./CartProvider";
import { formatPrice } from "@/utils/format";

export default function CouponEntry() {
  const { token, cart, busy } = useCart();
  const [code, setCode] = useState("");
  const [state, setState] = useState({});
  const [checking, setChecking] = useState(false);
  const fingerprint = JSON.stringify(cart.data.map(item => [item.id, item.quantity, item.price, item.available]));
  const applied = state.fingerprint === fingerprint && state.code === code.trim().toUpperCase() ? state.result : null;
  async function apply() {
    setChecking(true);
    setState({});
    try {
      const result = await api("/coupons/preview", { token, method: "POST", body: { coupon_code: code } });
      setState({ result: result.data, fingerprint, code: code.trim().toUpperCase() });
    } catch (e) { setState({ error: e.message }); }
    finally { setChecking(false); }
  }
  return (
    <div className="coupon-entry">
      <label htmlFor="coupon-code">Mã giảm giá</label>
      <div className="feature-actions">
        <input id="coupon-code" value={code} maxLength={40} placeholder="Nhập mã giảm giá"
          onChange={e => setCode(e.target.value.toUpperCase())} disabled={busy || checking} />
        <button type="button" disabled={busy || checking || !code.trim()} onClick={apply}>
          {checking ? "Đang kiểm tra…" : "Áp dụng"}
        </button>
        {applied && <button type="button" onClick={() => { setCode(""); setState({}); }}>Bỏ mã</button>}
      </div>
      <input type="hidden" name="coupon_code" value={applied?.code || ""} />
      {state.error && <p className="error-message" role="alert">{state.error}</p>}
      {applied && <div role="status" className="coupon-result">
        <p>Giảm giá: <strong>− {formatPrice(applied.discount)}</strong></p>
        <p>Tổng thanh toán: <strong>{formatPrice(applied.total)}</strong></p>
      </div>}
    </div>
  );
}
