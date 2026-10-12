import Link from "next/link";
import ProductCard from "@/components/shop/ProductCard";
import CategoryList from "@/components/shop/CategoryList";
import { getProductPage } from "@/services/productServices";
import { getCategories } from "@/services/categoryServices";
import { getAllPages } from "@/services/api";

export const metadata = { title: "Sản phẩm | HH Shop" };

export default async function ProductsPage({ searchParams }) {
  const query = await searchParams;
  const filters = {};
  for (const key of ["q", "category", "brand", "min_price", "max_price", "hot", "sale"]) {
    if (typeof query[key] === "string" && query[key].trim()) {
      filters[key] = query[key].trim();
    }
  }
  const pageValue = Number(query.page);
  const requestedPage = Number.isSafeInteger(pageValue) && pageValue > 0 && pageValue <= 2147483647 ? pageValue : 1;
  const params = { page: String(requestedPage) };
  const mapping = {
    q: "search",
    category: "category_id",
    brand: "brand_id",
    min_price: "min_price",
    max_price: "max_price",
    hot: "is_featured",
    sale: "on_sale",
  };
  for (const [key, value] of Object.entries(filters)) {
    params[mapping[key]] = value;
  }

  let [productsResult, categoriesResult, brandsResult] = await Promise.allSettled([
    getProductPage(params),
    getCategories(),
    getAllPages("/brands"),
  ]);
  if (productsResult.status === "fulfilled" && requestedPage > productsResult.value.meta.last_page) {
    [productsResult] = await Promise.allSettled([
      getProductPage({ ...params, page: String(productsResult.value.meta.last_page) }),
    ]);
  }
  const result = productsResult.status === "fulfilled" ? productsResult.value : null;
  const categories = categoriesResult.status === "fulfilled" ? categoriesResult.value : [];
  const brands = brandsResult.status === "fulfilled"
    ? brandsResult.value.filter((brand) => Number(brand.status) === 1)
    : [];

  function pageHref(page) {
    return `/products?${new URLSearchParams({ ...filters, page: String(page) })}`;
  }

  return (
    <div className="container catalog-page">
      <p className="breadcrumb"><Link href="/">Trang chủ</Link> / Sản phẩm</p>
      <span className="eyebrow">KHÁM PHÁ HH SHOP</span>
      <h1>Sản phẩm dành cho bạn</h1>
      <form action="/products" className="shop-filters">
        <div>
          <label htmlFor="catalog-q">Tìm sản phẩm</label>
          <input
            id="catalog-q"
            name="q"
            defaultValue={filters.q || ""}
            placeholder="Tên sản phẩm..."
            maxLength={255}
          />
        </div>
        <div>
          <label htmlFor="catalog-brand">Thương hiệu</label>
          <select id="catalog-brand" name="brand" defaultValue={filters.brand || ""}>
            <option value="">Tất cả thương hiệu</option>
            {brands.map((brand) => (
              <option key={brand.id} value={brand.id}>{brand.name}</option>
            ))}
          </select>
        </div>
        <div>
          <label htmlFor="catalog-min">Giá từ (₫)</label>
          <input
            id="catalog-min"
            name="min_price"
            type="number"
            min="0"
            step="1000"
            defaultValue={filters.min_price || ""}
            placeholder="0"
          />
        </div>
        <div>
          <label htmlFor="catalog-max">Giá đến (₫)</label>
          <input
            id="catalog-max"
            name="max_price"
            type="number"
            min="0"
            step="1000"
            defaultValue={filters.max_price || ""}
            placeholder="Không giới hạn"
          />
        </div>
        {["category", "hot", "sale"].map((key) => filters[key] ? (
          <input key={key} type="hidden" name={key} value={filters[key]} />
        ) : null)}
        <div className="shop-filter-actions">
          <button type="submit" className="primary-button">Tìm kiếm / Lọc</button>
          <Link href="/products">Xóa bộ lọc</Link>
        </div>
      </form>
      <CategoryList compact categories={categories} selected={filters.category || ""} filters={filters} />
      {categoriesResult.status === "rejected" && <p role="alert">Chưa tải được danh mục.</p>}
      {brandsResult.status === "rejected" && <p role="alert">Chưa tải được thương hiệu.</p>}
      {productsResult.status === "rejected" ? (
        <div className="empty-panel" role="alert">
          <p>{productsResult.reason.message}</p>
          <Link href={pageHref(requestedPage)}>Thử lại</Link>
        </div>
      ) : (
        <>
          <p className="result-count">
            {result.meta.total} sản phẩm{filters.q ? ` cho “${filters.q}”` : ""}
            {filters.hot === "1" ? " · Sản phẩm hot" : ""}
            {filters.sale === "1" ? " · Đang khuyến mãi" : ""}
          </p>
          {result.data.length ? (
            <div className="product-grid">
              {result.data.map((product) => <ProductCard key={product.id} product={product} />)}
            </div>
          ) : (
            <div className="empty-panel">
              <p>Chưa có sản phẩm phù hợp.</p>
              <Link href="/products">Xem tất cả sản phẩm</Link>
            </div>
          )}
          {result.meta.last_page > 1 && (
            <nav className="pagination" aria-label="Phân trang sản phẩm">
              {result.meta.current_page > 1 && (
                <Link href={pageHref(result.meta.current_page - 1)}>← Trang trước</Link>
              )}
              <span>Trang {result.meta.current_page} / {result.meta.last_page}</span>
              {result.meta.current_page < result.meta.last_page && (
                <Link href={pageHref(result.meta.current_page + 1)}>Trang sau →</Link>
              )}
            </nav>
          )}
        </>
      )}
    </div>
  );
}
