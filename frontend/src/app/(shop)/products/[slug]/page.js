import Link from "next/link";
import { notFound } from "next/navigation";
import { getProduct, getRelatedProducts } from "@/services/productServices";
import { formatPrice } from "@/utils/format";
import ProductCard, { ProductImage } from "@/components/shop/ProductCard";
import ProductReviews from "@/components/shop/ProductReviews";
import AddToCart from "@/components/shop/AddToCart";

export default async function ProductDetailPage({ params }) {
  const { slug } = await params;
  if (!slug) notFound();
  let product;
  let error;
  try {
    product = await getProduct(slug);
  } catch (e) {
    if (e.status === 404) notFound();
    error = e.message;
  }
  if (error)
    return (
      <div className="container catalog-page">
        <h1>Chi tiết sản phẩm</h1>
        <p
          role="alert"
          className="error-message"
        >
          {error}
        </p>
        <Link href={`/products/${encodeURIComponent(slug)}`}>Thử lại</Link>
      </div>
    );
  if (!product || Number(product.status) !== 1) notFound();

  let relatedProducts = [];
  let relatedError = "";
  try {
    relatedProducts = await getRelatedProducts(product);
  } catch {
    relatedError = "Chưa tải được sản phẩm liên quan. Vui lòng thử lại sau.";
  }

  return (
    <div className="container catalog-page">
      <p className="breadcrumb">
        <Link href="/">Trang chủ</Link> / <Link href="/products">Sản phẩm</Link> /{" "}
        {product.name}
      </p>
      <h1 className="product-detail-title">{product.name}</h1>
      <div className="product-detail">
        <ProductImage
          image={product.image}
          name={product.name}
        />
        <div className="product-purchase-card">
          {product.category_name && (
            <Link
              href={`/products?category=${product.category_id}`}
              className="detail-category"
            >
              {product.category_name}
            </Link>
          )}
          <p className="detail-price">{formatPrice(product.final_price ?? product.price_sale)}</p>
          <p className="detail-description">
            {product.description || "Thông tin giới thiệu đang được cập nhật."}
          </p>
          <dl className="product-specs">
            <div>
              <dt>Thương hiệu</dt>
              <dd>{product.brand_name || "Đang cập nhật"}</dd>
            </div>
            <div>
              <dt>Tình trạng</dt>
              <dd>
                {Number(product.qty) > 0 && Number(product.is_available) === 1
                  ? "Còn hàng"
                  : "Tạm hết hàng"}
              </dd>
            </div>
          </dl>
          <AddToCart product={product} />
        </div>
      </div>
      <section className="product-information">
        <h2 className="detail-heading">Thông tin sản phẩm</h2>
        <p className="detail-text">{product.detail || "Thông tin đang được cập nhật."}</p>
      </section>
      <ProductReviews productId={product.id} />
      <section className="home-section" aria-labelledby="related-products-title">
        <div className="section-heading">
          <h2 id="related-products-title">Sản phẩm liên quan</h2>
        </div>
        {relatedError ? (
          <p role="alert" className="error-message">{relatedError}</p>
        ) : relatedProducts.length ? (
          <div className="product-grid related-product-grid">
            {relatedProducts.map((item) => (
              <ProductCard
                key={item.id}
                product={item}
              />
            ))}
          </div>
        ) : (
          <p>Chưa có sản phẩm cùng danh mục.</p>
        )}
      </section>
    </div>
  );
}
