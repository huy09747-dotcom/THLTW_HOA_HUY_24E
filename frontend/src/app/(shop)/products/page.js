import Link from "next/link";
import ProductCard from "@/components/shop/ProductCard";
import CategoryList from "@/components/shop/CategoryList";
import { getProducts } from "@/services/productServices";
import { getCategories } from "@/services/categoryServices";

export const metadata = { title: "Sản phẩm | HH Shop" };
export default async function ProductsPage({ searchParams }) {
  const query = await searchParams;
  const category = typeof query.category === "string" ? query.category : "";
  const search = typeof query.q === "string" ? query.q.trim() : "";
  const pageValue = Number(query.page);
  const requestedPage = Number.isSafeInteger(pageValue) && pageValue > 0 ? pageValue : 1;
  const [productsResult, categoriesResult] = await Promise.allSettled([
    getProducts(),
    getCategories(),
  ]);
  const categories =
    categoriesResult.status === "fulfilled" ? categoriesResult.value : [];
  const products =
    productsResult.status === "fulfilled"
      ? productsResult.value.filter(
          (product) =>
            (!category || String(product.category_id) === category) &&
            (!search ||
              product.name
                .toLocaleLowerCase("vi")
                .includes(search.toLocaleLowerCase("vi"))),
        )
      : [];
  const totalPages = Math.max(1, Math.ceil(products.length / 12));
  const page = Math.min(requestedPage, totalPages);
  function pageHref(number) {
    const params = new URLSearchParams({ page: String(number) });
    if (category) params.set("category", category);
    if (search) params.set("q", search);
    return `/products?${params}`;
  }
  return (
    <div className="container catalog-page">
      <p className="breadcrumb">
        <Link href="/">Trang chủ</Link> / Sản phẩm
      </p>
      <span className="eyebrow">KHÁM PHÁ HH SHOP</span>
      <h1>Sản phẩm dành cho bạn</h1>
      <form
        action="/products"
        className="catalog-search"
      >
        <label
          className="sr-only"
          htmlFor="catalog-q"
        >
          Tên sản phẩm
        </label>
        <input
          id="catalog-q"
          name="q"
          defaultValue={search}
          placeholder="Tìm theo tên sản phẩm…"
        />
        {category && (
          <input
            type="hidden"
            name="category"
            value={category}
          />
        )}
        <button className="primary-button">Tìm kiếm</button>
      </form>
      <CategoryList
        categories={categories}
        selected={category}
      />
      {categoriesResult.status === "rejected" && (
        <p
          role="status"
          className="notice"
        >
          Chưa tải được danh mục.
        </p>
      )}
      {productsResult.status === "rejected" ? (
        <div
          className="empty-panel"
          role="alert"
        >
          <p>{productsResult.reason.message}</p>
          <Link href={pageHref(page)}>Thử lại</Link>
        </div>
      ) : (
        <>
          <p className="result-count">
            {products.length} sản phẩm{search ? ` cho “${search}”` : ""}
          </p>
          {products.length ? (
            <div className="product-grid">
              {products.slice((page - 1) * 12, page * 12).map((product) => (
                <ProductCard
                  key={product.id}
                  product={product}
                />
              ))}
            </div>
          ) : (
            <div className="empty-panel">
              <p>Chưa có sản phẩm phù hợp.</p>
              <Link href="/products">Xem tất cả sản phẩm</Link>
            </div>
          )}
          {totalPages > 1 && (
            <nav
              className="pagination"
              aria-label="Phân trang sản phẩm"
            >
              {page > 1 && <Link href={pageHref(page - 1)}>← Trang trước</Link>}
              <span>
                Trang {page} / {totalPages}
              </span>
              {page < totalPages && <Link href={pageHref(page + 1)}>Trang sau →</Link>}
            </nav>
          )}
        </>
      )}
    </div>
  );
}
