import Link from "next/link";
import { notFound } from "next/navigation";
import { getProduct } from "@/services/productServices";
import { formatPrice } from "@/utils/format";
import { ProductImage } from "@/components/shop/ProductCard";
import AddToCart from "@/components/shop/AddToCart";

export default async function ProductDetailPage({ params }) {
  const { id } = await params;
  if (!/^\d+$/.test(id)) notFound();
  let product;
  let error;
  try {
    product = await getProduct(id);
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
        <Link href={`/products/${id}`}>Thử lại</Link>
      </div>
    );
  if (!product || Number(product.status) !== 1) notFound();
  return (
    <div className="container catalog-page">
      <p className="breadcrumb">
        <Link href="/">Trang chủ</Link> / <Link href="/products">Sản phẩm</Link> /{" "}
        {product.name}
      </p>
      <div className="product-detail">
        <ProductImage
          image={product.image}
          name={product.name}
        />
        <div>
          <span className="eyebrow">{product.brand_name || "HH SHOP"}</span>
          <h1>{product.name}</h1>
          {product.category_name && (
            <Link
              href={`/products?category=${product.category_id}`}
              className="detail-category"
            >
              {product.category_name}
            </Link>
          )}
          <p className="detail-price">{formatPrice(product.price_sale)}</p>
          <p className="detail-description">
            {product.description || "Thông tin giới thiệu đang được cập nhật."}
          </p>
          <dl className="product-specs">
            <div>
              <dt>Thương hiệu</dt>
              <dd>{product.brand_name || "Đang cập nhật"}</dd>
            </div>
            <div>
              <dt>Mã sản phẩm</dt>
              <dd>{product.sku || product.id}</dd>
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
      <section className="home-section">
        <h2 className="detail-heading">Thông tin sản phẩm</h2>
        <p className="detail-text">{product.detail || "Thông tin đang được cập nhật."}</p>
      </section>
    </div>
  );
}
