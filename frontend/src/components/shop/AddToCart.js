"use client";

import ShopIcon from "./ShopIcon";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useCallback, useState } from "react";
import CartNotice from "./CartNotice";
import { useCart } from "./CartProvider";

export default function AddToCart({ product }) {
  const { user, loading, busy, error, changeCart } = useCart();
  const [quantity, setQuantity] = useState(1);
  const [added, setAdded] = useState(false);
  const closeNotice = useCallback(() => setAdded(false), []);
  const router = useRouter();
  const max = Math.min(999, Number(product.qty));
  const disabled =
    busy ||
    !Number.isInteger(Number(quantity)) ||
    Number(quantity) < 1 ||
    Number(quantity) > max;

  async function purchase(buyNow) {
    setAdded(false);
    const success = await changeCart("/cart/items", "POST", {
      product_id: product.id,
      quantity: Number(quantity),
    });
    if (success) {
      if (buyNow) router.push("/cart");
      else setAdded(true);
    }
  }

  if (!Number(product.is_available) || !max)
    return <p className="notice">Tạm hết hàng</p>;
  if (loading) return <p role="status">Đang tải…</p>;
  if (!user) {
    return (
      <Link
        className="primary-button"
        href={"/login?next=" + encodeURIComponent("/products/" + encodeURIComponent(product.slug))}
      >
        Đăng nhập để mua hàng
      </Link>
    );
  }

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
          onChange={(event) => {
            setQuantity(event.target.value);
            setAdded(false);
          }}
        />
        <button
          className="secondary-button card-add-button"
          title="Thêm vào giỏ hàng"
          aria-label={"Thêm " + product.name + " vào giỏ hàng"}
          disabled={disabled}
          onClick={() => purchase(false)}
        >
          <ShopIcon name="cartPlus" />
          <span>Thêm vào giỏ</span>
        </button>
        <button
          className="primary-button card-buy-button"
          disabled={disabled}
          onClick={() => purchase(true)}
        >
          Mua ngay
        </button>
      </div>
      {added && (
        <CartNotice message="Đã thêm vào giỏ hàng" onClose={closeNotice} />
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
