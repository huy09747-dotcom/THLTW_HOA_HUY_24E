"use client";

import Link from "next/link";
import { useState } from "react";
import { useCart } from "@/components/CartProvider";

export default function AddToCart({ product }) {
  const { user, loading, busy, error, changeCart } = useCart();
  const [quantity, setQuantity] = useState(1);
  const [added, setAdded] = useState(false);
  const max = Math.min(999, Number(product.qty));
  const available =
    Number(product.is_available) === 1 && max > 0 && product.price_sale != null;
  if (!available) return <p className="notice">Sản phẩm hiện chưa thể đặt mua.</p>;
  if (loading) return <p role="status">Đang kiểm tra tài khoản…</p>;
  if (!user)
    return (
      <Link
        className="primary-button"
        href={`/cart?next=${encodeURIComponent(`/products/${product.id}`)}`}
      >
        Đăng nhập để thêm vào giỏ
      </Link>
    );
  return (
    <div className="add-to-cart">
      <label htmlFor="quantity">Số lượng</label>
      <div className="purchase-row">
        <input
          id="quantity"
          type="number"
          min="1"
          max={max}
          value={quantity}
          disabled={busy}
          onChange={(e) => {
            setQuantity(e.target.value);
            setAdded(false);
          }}
        />
        <button
          className="primary-button"
          disabled={
            busy ||
            !Number.isInteger(Number(quantity)) ||
            Number(quantity) < 1 ||
            Number(quantity) > max
          }
          onClick={async () => {
            setAdded(false);
            setAdded(
              await changeCart("/cart/items", "POST", {
                product_id: product.id,
                quantity: Number(quantity),
              }),
            );
          }}
        >
          {busy ? "Đang thêm…" : "Thêm vào giỏ hàng"}
        </button>
      </div>
      {added && (
        <p
          role="status"
          className="success"
        >
          Đã thêm vào giỏ hàng. <Link href="/cart">Xem giỏ hàng →</Link>
        </p>
      )}
      {error && (
        <p
          role="alert"
          className="error-message"
        >
          {error}
        </p>
      )}
    </div>
  );
}
