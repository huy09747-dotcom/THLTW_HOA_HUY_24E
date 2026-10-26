"use client";

import Link from "next/link";
import { useRef, useState } from "react";
import { useRouter } from "next/navigation";
import { useCart } from "./CartProvider";
import { formatPrice, imageUrl } from "@/utils/format";

export function ProductImage({ image, name }) {
  const [failed, setFailed] = useState(null);
  const src = imageUrl(image);
  return (
    <div className="product-image">
      {src && failed !== src ? (
        // Ảnh có thể nằm trên host Laravel do người dùng cấu hình.
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={src}
          alt={name}
          loading="lazy"
          onError={() => setFailed(src)}
        />
      ) : (
        <div className="image-placeholder">
          <span aria-hidden="true">◇</span>
          <small>Chưa có hình ảnh</small>
        </div>
      )}
    </div>
  );
}
export default function ProductCard({ product }) {
  const { user, loading, busy, error, changeCart } = useCart();
  const router = useRouter();
  const lock = useRef(false);
  const [action, setAction] = useState(null);
  const [feedback, setFeedback] = useState("");
  const available = Number(product.qty) > 0 && Number(product.is_available) === 1;

  async function purchase(buyNow) {
    if (lock.current || busy || loading || !available) return;
    if (!user) { router.push("/login"); return; }
    lock.current = true;
    setAction(buyNow ? "buy" : "add");
    setFeedback("");
    try {
      const success = await changeCart("/cart/items", "POST", { product_id: product.id, quantity: 1 });
      if (success) {
        setFeedback("success");
        if (buyNow) router.push("/cart");
      } else setFeedback("error");
    } finally { lock.current = false; setAction(null); }
  }
  const onSale = Number(product.discount_percent) > 0
    && product.price_sale != null
    && product.final_price != null
    && Number(product.final_price) < Number(product.price_sale);

  return (
    <article className="product-card">
      <Link
        href={`/products/${encodeURIComponent(product.slug)}`}
        className="product-card-visual"
        aria-label={`Xem ${product.name}`}
      >
        {onSale && (
          <span className="product-sale-badge" aria-label={`Giảm ${product.discount_percent}%`}>
            -{product.discount_percent}%
          </span>
        )}
        <ProductImage
          image={product.image}
          name={product.name}
        />
      </Link>
      <div className="product-card-body">
        <p className="product-category">{product.category_name || "Sản phẩm"}</p>
        <h3>
          <Link href={`/products/${encodeURIComponent(product.slug)}`}>{product.name}</Link>
        </h3>
        {onSale ? (
          <div className="product-sale-prices">
            <p className="product-original-price">
              <span className="sr-only">Giá gốc: </span>
              <del>{formatPrice(product.price_sale)}</del>
            </p>
            <p className="product-price product-sale-price">
              <span className="sr-only">Giá sale: </span>
              {formatPrice(product.final_price)}
            </p>
          </div>
        ) : (
          <p className="product-price">{formatPrice(product.final_price ?? product.price_sale)}</p>
        )}
        <div className="product-card-bottom">
          <span>
            {Number(product.qty) > 0 && Number(product.is_available) === 1
              ? "Còn hàng"
              : "Tạm hết hàng"}
          </span>

        </div>
        <div className="product-card-actions">
          <button type="button" className="secondary-button" disabled={loading || busy || !!action || !available} onClick={() => purchase(false)} aria-label={"Thêm " + product.name + " vào giỏ hàng"}>
            {action === "add" ? "Đang thêm…" : "Thêm vào giỏ hàng"}
          </button>
          <button type="button" className="primary-button" disabled={loading || busy || !!action || !available} onClick={() => purchase(true)} aria-label={"Mua ngay " + product.name}>
            {action === "buy" ? "Đang xử lý…" : "Mua ngay"}
          </button>
        </div>
        {feedback === "success" && <p className="product-card-feedback" role="status">Đã thêm vào giỏ hàng.</p>}
        {feedback === "error" && <p className="product-card-feedback error-message" role="alert">{error || "Chưa thêm được sản phẩm. Vui lòng thử lại."}</p>}
      </div>
    </article>
  );
}
