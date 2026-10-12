import Link from "next/link";
import ProductCard from "@/components/shop/ProductCard";
import CategoryList from "@/components/shop/CategoryList";
import { getProductPage } from "@/services/productServices";
import { getCategories } from "@/services/categoryServices";

export default async function Home() {
  const [newResult, hotResult, saleResult, categoriesResult] = await Promise.allSettled([
    getProductPage(),
    getProductPage({ is_featured: "1" }),
    getProductPage({ on_sale: "1" }),
    getCategories(),
  ]);
  const categories = categoriesResult.status === "fulfilled" ? categoriesResult.value : [];
  const sections = [
    { id: "new", title: "Sản phẩm mới", result: newResult, href: "/products" },
    { id: "hot", title: "Sản phẩm hot", result: hotResult, href: "/products?hot=1" },
    { id: "sale", title: "Sản phẩm sale", result: saleResult, href: "/products?sale=1" },
  ];
  return (
    <div className="container home-page">
      <section
        className="hero"
        aria-labelledby="hero-title"
      >
        <div className="hero-copy">
          <span className="eyebrow">CHÀO MỪNG ĐẾN VỚI HH SHOP</span>
          <h1 id="hero-title">
            Công nghệ mới.
            <br />
            <span>Cảm hứng mỗi ngày.</span>
          </h1>
          <p>
            Khám phá những sản phẩm công nghệ phù hợp với phong cách và nhu cầu của bạn.
          </p>
          <Link
            className="primary-button"
            href="/products"
          >
            Khám phá sản phẩm <span aria-hidden="true">↗</span>
          </Link>
          <span className="hero-caption">KẾT NỐI · SÁNG TẠO · TRẢI NGHIỆM</span>
        </div>
        <div
          className="hero-art"
          aria-hidden="true"
        >
          <div className="orbit" />
          <div className="orbit orbit-outer" />
          <div className="phone phone-back">
            <div className="camera">
              <i />
              <i />
              <i />
            </div>
          </div>
          <div className="phone phone-front">
            <div className="phone-island" />
            <div className="phone-glow" />
            <span>
              HH<small>EXPLORE MORE</small>
            </span>
          </div>
          <span className="art-caption">YOUR EVERYDAY INSPIRATION</span>
        </div>
      </section>
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
            <div className="product-grid">
              {result.value.data.slice(0, 4).map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          ) : (
            <div className="empty-panel"><p>Chưa có {title.toLowerCase()}.</p></div>
          )}
        </section>
      ))}
    </div>
  );
}
