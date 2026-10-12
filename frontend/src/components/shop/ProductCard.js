"use client";

import Link from "next/link";
import { useState } from "react";
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
          <Link href={`/products/${encodeURIComponent(product.slug)}`}>Xem chi tiết ↗</Link>
        </div>
      </div>
    </article>
  );
}
