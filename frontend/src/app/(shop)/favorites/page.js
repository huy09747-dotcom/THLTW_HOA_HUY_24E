"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import { api } from "@/services/api";
import ProductCard from "@/components/shop/ProductCard";

export default function FavoritesPage() {
  const { user, token, loading, favoriteIds, favoriteError } = useCart();
  const [page, setPage] = useState(1);
  const [state, setState] = useState({});
  const idsKey = favoriteIds.join(",");
  useEffect(() => {
    if (!token) return;
    let active = true;
    api(`/favorites?page=${page}`, { token }).then(result => {
      if (!active) return;
      if (page > (result.meta?.last_page || 1)) setPage(result.meta?.last_page || 1);
      else setState({ result, page, idsKey, token });
    }).catch(e => { if (active) setState({ error: e.message, page, idsKey, token }); });
    return () => { active = false; };
  }, [token, page, idsKey]);
  if (loading) return <p className="container catalog-page">Đang tải…</p>;
  if (!user) return <section className="auth-page"><h1>Sản phẩm yêu thích</h1><Link href="/login">Đăng nhập để xem</Link></section>;
  const pending = state.page !== page || state.idsKey !== idsKey || state.token !== token;
  return (
    <section className="container catalog-page">
      <h1>Sản phẩm yêu thích</h1>
      {(state.error || favoriteError) && <p role="alert" className="error-message">{state.error || favoriteError}</p>}
      {pending ? <p role="status">Đang tải danh sách…</p> : state.result && <>
        {!state.result.data.length && <p>Bạn chưa lưu sản phẩm nào. <Link href="/products">Khám phá sản phẩm →</Link></p>}
        <div className="product-grid">{state.result.data.map(product => <ProductCard key={product.id} product={product} />)}</div>
        {state.result.meta.last_page > 1 && <div className="feature-actions">
          <button disabled={page <= 1} onClick={() => setPage(p => p - 1)}>Trang trước</button>
          <span>Trang {page} / {state.result.meta.last_page}</span>
          <button disabled={page >= state.result.meta.last_page} onClick={() => setPage(p => p + 1)}>Trang sau</button>
        </div>}
      </>}
    </section>
  );
}
