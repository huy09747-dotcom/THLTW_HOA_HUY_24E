import Link from "next/link";
import ProductCard from "@/components/shop/ProductCard";
import CategoryList from "@/components/shop/CategoryList";
import { getProducts } from "@/services/productServices";
import { getCategories } from "@/services/categoryServices";

export default async function Home() {
  const [productsResult, categoriesResult] = await Promise.allSettled([
    getProducts(),
    getCategories(),
  ]);
  const products = productsResult.status === "fulfilled" ? productsResult.value : [];
  const categories =
    categoriesResult.status === "fulfilled" ? categoriesResult.value : [];
  const featured = products.filter((product) => Number(product.is_featured) === 1);
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
          <CategoryList categories={categories} />
        ) : (
          <div className="empty-panel">
            <p>Chưa có danh mục sản phẩm.</p>
          </div>
        )}
      </section>
      <section
        id="san-pham"
        className="home-section"
        aria-labelledby="products-title"
      >
        <div className="section-heading">
          <div>
            <span className="eyebrow">DÀNH CHO BẠN</span>
            <h2 id="products-title">Sản phẩm mới</h2>
          </div>
          <Link href="/products">Xem tất cả ↗</Link>
        </div>
        {productsResult.status === "rejected" ? (
          <p
            role="alert"
            className="error-message"
          >
            {productsResult.reason.message}
          </p>
        ) : products.length ? (
          <div className="product-grid">
            {products.slice(0, 4).map((product) => (
              <ProductCard
                key={product.id}
                product={product}
              />
            ))}
          </div>
        ) : (
          <div className="empty-panel">
            <p>Chưa có sản phẩm.</p>
          </div>
        )}
      </section>
      <section
        className="home-section"
        aria-labelledby="featured-title"
      >
        <div className="section-heading">
          <div>
            <span className="eyebrow">GỢI Ý KHÁM PHÁ</span>
            <h2 id="featured-title">Sản phẩm nổi bật</h2>
          </div>
        </div>
        {productsResult.status === "rejected" ? (
          <p className="notice">Chưa tải được sản phẩm nổi bật.</p>
        ) : featured.length ? (
          <div className="product-grid">
            {featured.slice(0, 4).map((product) => (
              <ProductCard
                key={product.id}
                product={product}
              />
            ))}
          </div>
        ) : (
          <div className="empty-panel">
            <p>Chưa có sản phẩm nổi bật.</p>
          </div>
        )}
      </section>
    </div>
  );
}
