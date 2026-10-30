"use client";

import { useEffect, useRef, useState } from "react";
import { useCart } from "./CartProvider";
import { api } from "@/services/api";
import { formatPrice } from "@/utils/format";

const labels = {
  cod: "Thanh toán khi nhận hàng",
  payos: "Chuyển khoản ngân hàng",
  momo: "MoMo sandbox",
  paypal: "PayPal sandbox",
  vnpay: "Thẻ nội địa / VNPAY sandbox",
};

export default function OrderPayment({ order, onUpdated }) {
  const { token } = useCart();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  const [offer, setOffer] = useState(null);
  const updated = useRef(onUpdated);

  useEffect(() => {
    updated.current = onUpdated;
  }, [onUpdated]);

  useEffect(() => {
    if (!token || order.payment_method !== "payos" || order.payment_status !== "unpaid") return;
    let active = true;
    let timer;
    let attempts = 0;

    async function check() {
      if (!active) return;
      if (document.visibilityState === "visible") {
        try {
          const result = await api(`/my-orders/${order.id}/payment/verify`, {
            method: "POST",
            token,
          });
          if (!active) return;
          if (result.data.payment_status === "paid") {
            setNotice("Đã xác nhận thanh toán.");
            updated.current?.();
            return;
          }
        } catch {
          // Manual verification remains available after a temporary connection error.
        }
      }
      attempts += 1;
      if (active && attempts < 60) timer = window.setTimeout(check, 10000);
    }

    timer = window.setTimeout(check, 1500);
    return () => {
      active = false;
      window.clearTimeout(timer);
    };
  }, [token, order.id, order.payment_method, order.payment_status]);

  async function action(verify = false) {
    setBusy(true);
    setError("");
    setNotice("");
    try {
      const result = await api(`/my-orders/${order.id}/payment${verify ? "/verify" : ""}`, {
        method: "POST",
        token,
        ...(!verify ? { body: { return_origin: window.location.origin } } : {}),
      });
      if (verify) {
        setNotice(result.data.payment_status === "paid"
          ? "Đã xác nhận thanh toán."
          : "Chưa nhận được xác nhận thanh toán.");
        onUpdated?.();
      } else {
        setOffer(result.data);
        window.location.assign(result.data.url);
      }
    } catch (exception) {
      setError(exception.message);
    } finally {
      setBusy(false);
    }
  }

  const payable = order.payment_method !== "cod"
    && order.payment_status === "unpaid"
    && Number(order.status) !== 5;

  return (
    <section className="commerce-panel">
      <h3>{labels[order.payment_method] || order.payment_method}</h3>
      {order.payment_status === "paid" && <p role="status">Đã thanh toán</p>}
      {payable && (
        <>
          <div className="auth-links">
            <button className="primary-button" disabled={busy} onClick={() => action()}>
              {busy ? "Đang xử lý…" : "Thanh toán ngay"}
            </button>
            <button disabled={busy} onClick={() => action(true)}>Kiểm tra thanh toán</button>
          </div>
          {offer && (
            <div>
              <p>
                Tổng thanh toán: <strong>{offer.currency === "USD"
                  ? `${(offer.amount / 100).toFixed(2)} USD`
                  : formatPrice(offer.amount)}</strong>
              </p>
              <a className="primary-button" href={offer.url}>
                {order.payment_method === "payos" ? "Mở mã QR thanh toán" : "Đến cổng thanh toán"} ↗
              </a>
            </div>
          )}
        </>
      )}
      {error && <p role="alert" className="error-message">{error}</p>}
      {notice && <p role="status">{notice}</p>}
    </section>
  );
}
