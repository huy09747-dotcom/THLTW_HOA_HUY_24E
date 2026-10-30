"use client";

import Link from "next/link";
import { useEffect } from "react";
import { createPortal } from "react-dom";

export default function CartNotice({ message, success = true, onClose }) {
  useEffect(() => {
    const timer = window.setTimeout(onClose, success ? 4000 : 7000);
    return () => window.clearTimeout(timer);
  }, [onClose, success]);

  if (typeof document === "undefined") return null;

  return createPortal(
    <div className={`cart-notice${success ? "" : " cart-notice-error"}`}>
      <span className="cart-notice-icon" aria-hidden="true">{success ? "✓" : "!"}</span>
      <div role={success ? "status" : "alert"} className="cart-notice-content">
        <strong>{message}</strong>
        {success && <Link href="/cart">Xem giỏ hàng →</Link>}
      </div>
      <button type="button" onClick={onClose} aria-label="Đóng thông báo">×</button>
    </div>,
    document.body,
  );
}
