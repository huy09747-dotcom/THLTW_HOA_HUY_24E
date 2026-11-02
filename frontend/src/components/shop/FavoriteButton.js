"use client";

import { useRouter } from "next/navigation";
import { useCart } from "./CartProvider";
import ShopIcon from "./ShopIcon";

export default function FavoriteButton({ productId }) {
  const { user, loading, favoriteIds, favoriteBusy, favoriteError, toggleFavorite } = useCart();
  const router = useRouter();
  const selected = favoriteIds.includes(Number(productId));
  return (
    <button type="button" className={`favorite-button ${selected ? "is-favorite" : ""}`}
      disabled={loading || favoriteBusy} aria-pressed={selected}
      title={favoriteError || (selected ? "Bỏ yêu thích" : "Lưu yêu thích")}
      aria-label={selected ? "Bỏ yêu thích" : "Lưu sản phẩm yêu thích"}
      onClick={() => user ? toggleFavorite(productId) : router.push("/login")}>
      <ShopIcon name="heart" />
    </button>
  );
}
