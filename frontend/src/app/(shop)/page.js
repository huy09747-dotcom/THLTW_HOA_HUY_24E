import Link from "next/link";
import ProductCarousel from "@/components/shop/ProductCarousel";
import CategoryList from "@/components/shop/CategoryList";
import { getProductPage } from "@/services/productServices";
import { getCategories } from "@/services/categoryServices";

import BannerCarousel from "@/components/shop/BannerCarousel";
import { api } from "@/services/api";

export default async function Home() {
  const [newResult, hotResult, saleResult, categoriesResult, bannersResult, newsResult] = await Promise.allSettled([
    getProductPage(),
    getProductPage({ is_featured: "1" }),
    getProductPage({ on_sale: "1" }),
    getCategories(),
    api("/banners"),
    api("/news"),
  ]);
  const categories = categoriesResult.status === "fulfilled" ? categoriesResult.value : [];
  const sections = [
    { id: "new", title: "Sản phẩm mới", result: newResult, href: "/products" },
    { id: "hot", title: "Sản phẩm hot", result: hotResult, href: "/products?hot=1" },
    { id: "sale", title: "Sản phẩm sale", result: saleResult, href: "/products?sale=1" },
  ];
  return (
    <div className="container home-page">
      <BannerCarousel banners={bannersResult.status === "fulfilled" ? bannersResult.value : []} />
      <section
        id="danh-muc"
        className="home-section"
        aria-labelledby="category-title"
      >
        <div className="section-heading">
          <div>
            <span className="eyebrow">BẮT ĐẦU KHÁM PHÁ</span>
            <h2 id="category-title">Danh mục sản phẩm</h2>
          </div>
          <span className="section-number">01 /</span>
        </div>
        {categoriesResult.status === "rejected" ? (
          <p
            role="alert"
            className="error-message"
          >
            {categoriesResult.reason.message}
          </p>
        ) : categories.length ? (
          <CategoryList
            compact
            categories={categories}
          />
        ) : (
          <div className="empty-panel">
            <p>Chưa có danh mục sản phẩm.</p>
          </div>
        )}
      </section>
      {sections.map(({ id, title, result, href }) => (
        <section key={id} className="home-section" aria-labelledby={`${id}-title`}>
          <div className="section-heading">
            <h2 id={`${id}-title`}>{title}</h2>
            <Link href={href}>Xem tất cả ↗</Link>
          </div>
          {result.status === "rejected" ? (
            <p role="alert" className="error-message">{result.reason.message}</p>
          ) : result.value.data.length ? (
            <ProductCarousel products={result.value.data} label={title} />
          ) : (
            <div className="empty-panel"><p>Chưa có {title.toLowerCase()}.</p></div>
          )}
        </section>
      ))}
      <section className="home-section">
        <div className="section-heading"><h2>Bài viết mới nhất</h2><Link href="/news">Xem tất cả ↗</Link></div>
        {newsResult.status === "rejected" ? <p role="alert">Chưa tải được bài viết.</p> : <div className="news-grid">
          {newsResult.value.data.slice(0,3).map(post => <article className="feature-panel" key={post.id}>
            <h3><Link href={`/news/${encodeURIComponent(post.slug)}`}>{post.title}</Link></h3><p>{post.description}</p><Link href={`/news/${encodeURIComponent(post.slug)}`}>Đọc bài viết →</Link>
          </article>)}
          {!newsResult.value.data.length && <p>Chưa có bài viết.</p>}
        </div>}
      </section>
    </div>
  );
}
