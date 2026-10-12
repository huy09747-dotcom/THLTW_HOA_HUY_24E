"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { formatPrice } from "@/utils/format";
import AdminImage from "./AdminImage";
import styles from "@/app/admin/admin.module.css";

async function allOptions(path, token) {
  const first = await adminApi(path, token);
  const items = [...first.data];

  for (
    let page = 2;
    page <= (first.meta?.last_page || 1);
    page++
  ) {
    const next = await adminApi(
      path + (path.includes("?") ? "&" : "?") + "page=" + page,
      token,
    );

    items.push(...next.data);
  }

  return items;
}

export default function ManagementPage({
  title,
  endpoint,
  Form,
  product = false,
  category = false,
  trash = false,
}) {
  const resourcePath = endpoint.replace(/-trash$/, "");
  const { token } = useAdminAuth();

  const [page, setPage] = useState(1);
  const [revision, setRevision] = useState(0);
  const [result, setResult] = useState(null);
  const [options, setOptions] = useState({});
  const [editing, setEditing] = useState(null);
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");

  // =========================================================
  // BỘ LỌC SẢN PHẨM
  // =========================================================

  const [filters, setFilters] = useState({
    search: "",
    category_id: "",
    brand_id: "",
    status: "",
    is_available: "",
    is_featured: "",
  });

  const [appliedFilters, setAppliedFilters] = useState({
    search: "",
    category_id: "",
    brand_id: "",
    status: "",
    is_available: "",
    is_featured: "",
  });

  // =========================================================
  // BỘ LỌC DANH MỤC / THƯƠNG HIỆU
  // =========================================================

  const [entityFilters, setEntityFilters] = useState({
    search: "",
    status: "",
  });

  const [appliedEntityFilters, setAppliedEntityFilters] =
    useState({
      search: "",
      status: "",
    });

  // =========================================================
  // LOAD DATA
  // =========================================================

  useEffect(() => {
    let active = true;

    async function load() {
      setLoading(true);
      setError("");

      try {
        const params = new URLSearchParams();

        params.set("page", page);

        // =====================================================
        // FILTER SẢN PHẨM
        // =====================================================

        if (product && !trash) {
          if (appliedFilters.search.trim()) {
            params.set(
              "search",
              appliedFilters.search.trim(),
            );
          }

          if (appliedFilters.category_id) {
            params.set(
              "category_id",
              appliedFilters.category_id,
            );
          }

          if (appliedFilters.brand_id) {
            params.set(
              "brand_id",
              appliedFilters.brand_id,
            );
          }

          if (appliedFilters.status !== "") {
            params.set(
              "status",
              appliedFilters.status,
            );
          }

          if (appliedFilters.is_available !== "") {
            params.set(
              "is_available",
              appliedFilters.is_available,
            );
          }

          if (appliedFilters.is_featured !== "") {
            params.set(
              "is_featured",
              appliedFilters.is_featured,
            );
          }
        }

        // =====================================================
        // FILTER DANH MỤC
        // =====================================================

        if (category && !trash) {
          if (appliedEntityFilters.search.trim()) {
            params.set(
              "search",
              appliedEntityFilters.search.trim(),
            );
          }

          if (appliedEntityFilters.status !== "") {
            params.set(
              "status",
              appliedEntityFilters.status,
            );
          }
        }

        // =====================================================
        // FILTER THƯƠNG HIỆU
        // =====================================================

        if (!product && !category && !trash) {
          if (appliedEntityFilters.search.trim()) {
            params.set(
              "search",
              appliedEntityFilters.search.trim(),
            );
          }

          if (appliedEntityFilters.status !== "") {
            params.set(
              "status",
              appliedEntityFilters.status,
            );
          }
        }

        // =====================================================
        // GỌI API
        // =====================================================

        const data = await adminApi(
          endpoint + "?" + params.toString(),
          token,
        );

        let choices = {};

        if (product && !trash) {
          const [categories, brands] = await Promise.all([
            allOptions("/categories", token),
            allOptions("/brands", token),
          ]);

          choices = {
            category_id: categories,
            brand_id: brands,
          };
        }

        if (category && !trash) {
          const [visible, hidden] = await Promise.all([
            allOptions("/categories?status=1", token),
            allOptions("/categories?status=0", token),
          ]);
          choices.parent_id = [...visible, ...hidden];
        }

        if (active) {
          const lastPage =
            data.meta?.last_page || 1;

          if (page > lastPage) {
            setPage(lastPage);
          } else {
            setResult(data);
            setOptions(choices);
          }
        }
      } catch (exception) {
        if (active) {
          setResult(null);
          setError(exception.message);
        }
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    }

    load();

    return () => {
      active = false;
    };
  }, [
    endpoint,
    token,
    page,
    revision,
    product,
    appliedFilters,
    appliedEntityFilters,
    category,
    trash,
  ]);

  // =========================================================
  // CRUD
  // =========================================================

  async function mutate(
    path,
    method,
    body,
    message,
  ) {
    setBusy(true);
    setError("");
    setNotice("");

    try {
      await adminApi(path, token, {
        method,
        body,
      });

      setEditing(null);
      setNotice(message);
      setRevision((value) => value + 1);
    } catch (exception) {
      setError(exception.message);
    } finally {
      setBusy(false);
    }
  }

  async function save(body) {
    await mutate(
      editing.id
        ? endpoint + "/" + editing.id
        : endpoint,
      editing.id ? "PUT" : "POST",
      body,
      "Đã lưu thành công.",
    );
  }

  function remove(item) {
    const message = trash
      ? "Xóa vĩnh viễn " +
        item.name +
        "? Không thể khôi phục sau thao tác này."
      : "Xóa " + item.name + "?";

    if (window.confirm(message)) {
      mutate(
        trash
          ? resourcePath + "/" + item.id + "/force"
          : endpoint + "/" + item.id,
        "DELETE",
        undefined,
        trash
          ? "Đã xóa vĩnh viễn."
          : "Đã xóa thành công.",
      );
    }
  }

  // =========================================================
  // FILTER PRODUCT
  // =========================================================

  function applyFilters(event) {
    event.preventDefault();

    setPage(1);

    setAppliedFilters({
      ...filters,
      search: filters.search.trim(),
    });

    setEditing(null);
    setError("");
    setNotice("");
  }

  function clearFilters() {
    const emptyFilters = {
      search: "",
      category_id: "",
      brand_id: "",
      status: "",
      is_available: "",
      is_featured: "",
    };

    setFilters(emptyFilters);
    setAppliedFilters(emptyFilters);

    setPage(1);
    setEditing(null);
    setError("");
    setNotice("");
  }

  // =========================================================
  // FILTER CATEGORY / BRAND
  // =========================================================

  function applyEntityFilters(event) {
    event.preventDefault();

    setPage(1);

    setAppliedEntityFilters({
      ...entityFilters,
      search: entityFilters.search.trim(),
    });

    setEditing(null);
    setError("");
    setNotice("");
  }

  function clearEntityFilters() {
    const emptyFilters = {
      search: "",
      status: "",
    };

    setEntityFilters(emptyFilters);
    setAppliedEntityFilters(emptyFilters);

    setPage(1);
    setEditing(null);
    setError("");
    setNotice("");
  }

  return (
    <div>
      {/* =====================================================
          HEADER
      ===================================================== */}

      <div className={styles.toolbar}>
        <h1 className={styles.title}>
          {title}
        </h1>

        <div className={styles.actions}>
          {(category || product || resourcePath === "/brands") && (
            <Link
              href={trash ? "/admin" + resourcePath : "/admin" + resourcePath + "/trash"}
              className={styles.button}
            >
              <span className={styles.iconOnly}>
                {trash ? (
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    aria-hidden="true"
                  >
                    <path d="M3 6h18" />
                    <path d="M8 6V4h8v2" />
                    <path d="M19 6l-1 15H6L5 6" />
                    <path d="M10 11v6" />
                    <path d="M14 11v6" />
                  </svg>
                ) : (
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    aria-hidden="true"
                  >
                    <path d="M3 12h18" />
                    <path d="M13 6l6 6-6 6" />
                  </svg>
                )}
              </span>

              <span>
                {trash ? "Quay lại danh sách" : "Thùng rác"}
              </span>
            </Link>
          )}

          {!trash && (
            <button
              className={styles.primaryButton}
              disabled={busy || loading}
              onClick={() => {
                setEditing({});
                setError("");
                setNotice("");
              }}
            >
              <span className={styles.iconOnly}>
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  aria-hidden="true"
                >
                  <path d="M12 5v14" />
                  <path d="M5 12h14" />
                </svg>
              </span>

              <span>Thêm mới</span>
            </button>
          )}
        </div>
      </div>

      {/* =====================================================
          FILTER SẢN PHẨM
      ===================================================== */}

      {product && !trash && (
        <form
          className={styles.filterBox}
          onSubmit={applyFilters}
        >
          <div className={styles.filterGrid}>
            <div className={styles.filterField}>
              <label htmlFor="product-search">
                Tìm kiếm
              </label>

              <input
                id="product-search"
                type="search"
                value={filters.search}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    search: event.target.value,
                  }))
                }
                placeholder="Tên, slug hoặc SKU..."
              />
            </div>

            <div className={styles.filterField}>
              <label htmlFor="product-category">
                Danh mục
              </label>

              <select
                id="product-category"
                value={filters.category_id}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    category_id: event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả danh mục
                </option>

                {(options.category_id || []).map(
                  (item) => (
                    <option
                      key={item.id}
                      value={item.id}
                    >
                      {item.name}
                    </option>
                  ),
                )}
              </select>
            </div>

            <div className={styles.filterField}>
              <label htmlFor="product-brand">
                Thương hiệu
              </label>

              <select
                id="product-brand"
                value={filters.brand_id}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    brand_id: event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả thương hiệu
                </option>

                {(options.brand_id || []).map(
                  (item) => (
                    <option
                      key={item.id}
                      value={item.id}
                    >
                      {item.name}
                    </option>
                  ),
                )}
              </select>
            </div>

            <div className={styles.filterField}>
              <label htmlFor="product-status">
                Trạng thái
              </label>

              <select
                id="product-status"
                value={filters.status}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    status: event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả trạng thái
                </option>
                <option value="1">
                  Hoạt động
                </option>
                <option value="0">
                  Ẩn
                </option>
              </select>
            </div>

            <div className={styles.filterField}>
              <label htmlFor="product-available">
                Cho phép đặt mua
              </label>

              <select
                id="product-available"
                value={filters.is_available}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    is_available:
                      event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả
                </option>
                <option value="1">
                  Cho phép đặt mua
                </option>
                <option value="0">
                  Không cho đặt mua
                </option>
              </select>
            </div>

            <div className={styles.filterField}>
              <label htmlFor="product-featured">
                Sản phẩm nổi bật
              </label>

              <select
                id="product-featured"
                value={filters.is_featured}
                onChange={(event) =>
                  setFilters((current) => ({
                    ...current,
                    is_featured:
                      event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả
                </option>
                <option value="1">
                  Sản phẩm nổi bật
                </option>
                <option value="0">
                  Không nổi bật
                </option>
              </select>
            </div>
          </div>

          <div className={styles.filterActions}>
            <button
              type="submit"
              className={styles.primaryButton}
              disabled={busy || loading}
            >
              <span className={styles.iconOnly}>
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  aria-hidden="true"
                >
                  <circle
                    cx="11"
                    cy="11"
                    r="7"
                  />
                  <path d="m20 20-4-4" />
                </svg>
              </span>

              <span>Tìm kiếm</span>
            </button>

            <button
              type="button"
              className={styles.button}
              disabled={busy || loading}
              onClick={clearFilters}
            >
              Xóa bộ lọc
            </button>
          </div>
        </form>
      )}

      {/* =====================================================
          FILTER DANH MỤC
      ===================================================== */}

      {category && !trash && (
        <form
          className={styles.filterBox}
          onSubmit={applyEntityFilters}
        >
          <div className={styles.filterGrid}>
            <div className={styles.filterField}>
              <label htmlFor="category-search">
                Tìm kiếm
              </label>

              <input
                id="category-search"
                type="search"
                value={entityFilters.search}
                onChange={(event) =>
                  setEntityFilters((current) => ({
                    ...current,
                    search: event.target.value,
                  }))
                }
                placeholder="Tên hoặc slug..."
              />
            </div>

            <div className={styles.filterField}>
              <label htmlFor="category-status">
                Trạng thái
              </label>

              <select
                id="category-status"
                value={entityFilters.status}
                onChange={(event) =>
                  setEntityFilters((current) => ({
                    ...current,
                    status: event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả trạng thái
                </option>
                <option value="1">
                  Hoạt động
                </option>
                <option value="0">
                  Ẩn
                </option>
              </select>
            </div>
          </div>

          <div className={styles.filterActions}>
            <button
              type="submit"
              className={styles.primaryButton}
              disabled={busy || loading}
            >
              <span className={styles.iconOnly}>
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  aria-hidden="true"
                >
                  <circle
                    cx="11"
                    cy="11"
                    r="7"
                  />
                  <path d="m20 20-4-4" />
                </svg>
              </span>

              <span>Tìm kiếm</span>
            </button>

            <button
              type="button"
              className={styles.button}
              disabled={busy || loading}
              onClick={clearEntityFilters}
            >
              Xóa bộ lọc
            </button>
          </div>
        </form>
      )}

      {/* =====================================================
          FILTER THƯƠNG HIỆU
      ===================================================== */}

      {!product && !category && !trash && (
        <form
          className={styles.filterBox}
          onSubmit={applyEntityFilters}
        >
          <div className={styles.filterGrid}>
            <div className={styles.filterField}>
              <label htmlFor="brand-search">
                Tìm kiếm
              </label>

              <input
                id="brand-search"
                type="search"
                value={entityFilters.search}
                onChange={(event) =>
                  setEntityFilters((current) => ({
                    ...current,
                    search: event.target.value,
                  }))
                }
                placeholder="Tên hoặc slug..."
              />
            </div>

            <div className={styles.filterField}>
              <label htmlFor="brand-status">
                Trạng thái
              </label>

              <select
                id="brand-status"
                value={entityFilters.status}
                onChange={(event) =>
                  setEntityFilters((current) => ({
                    ...current,
                    status: event.target.value,
                  }))
                }
              >
                <option value="">
                  Tất cả trạng thái
                </option>
                <option value="1">
                  Hoạt động
                </option>
                <option value="0">
                  Ẩn
                </option>
              </select>
            </div>
          </div>

          <div className={styles.filterActions}>
            <button
              type="submit"
              className={styles.primaryButton}
              disabled={busy || loading}
            >
              <span className={styles.iconOnly}>
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  aria-hidden="true"
                >
                  <circle
                    cx="11"
                    cy="11"
                    r="7"
                  />
                  <path d="m20 20-4-4" />
                </svg>
              </span>

              <span>Tìm kiếm</span>
            </button>

            <button
              type="button"
              className={styles.button}
              disabled={busy || loading}
              onClick={clearEntityFilters}
            >
              Xóa bộ lọc
            </button>
          </div>
        </form>
      )}

      {/* =====================================================
          ERROR / NOTICE
      ===================================================== */}

      {error && (
        <p
          role="alert"
          className={styles.error}
        >
          {error}
        </p>
      )}

      {notice && (
        <p
          role="status"
          className={styles.success}
        >
          {notice}
        </p>
      )}

      {/* =====================================================
          FORM
      ===================================================== */}

      {editing && (
        <Form
          key={editing.id || "new"}
          initial={editing}
          options={options}
          busy={busy}
          onSave={save}
          onCancel={() =>
            setEditing(null)
          }
        />
      )}

      {/* =====================================================
          DATA
      ===================================================== */}

      {loading ? (
        <p role="status">
          Đang tải dữ liệu…
        </p>
      ) : !result ? (
        <button
          className={styles.button}
          onClick={() =>
            setRevision(
              (value) => value + 1,
            )
          }
        >
          Thử lại
        </button>
      ) : (
        <>
          <div className={styles.tableWrap}>
            <table className={styles.table}>
              <caption className="sr-only">
                {title}
              </caption>

              <thead>
                <tr>
                  <th>STT</th>
                  <th>Hình ảnh</th>
                  <th>Tên</th>
                  <th>Slug</th>

                  {product && (
                    <>
                      <th>
                        Danh mục / Thương hiệu
                      </th>

                      <th>
                        Giá bán / Tồn
                      </th>
                      <th>Giá sale</th>
                    </>
                  )}

                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
              </thead>

              <tbody>
                {result.data.length === 0 && (
                  <tr>
                    <td
                      colSpan={
                        product ? 9 : 6
                      }
                    >
                      {product
                        ? "Không tìm thấy sản phẩm phù hợp."
                        : "Chưa có dữ liệu."}
                    </td>
                  </tr>
                )}

                {result.data.map(
                  (item, index) => (
                    <tr key={item.id}>
                      <td>{(result.meta?.from ?? 1) + index}</td>

                      <td>
                        <AdminImage
                          image={item.image}
                          name={item.name}
                        />
                      </td>

                      <td>{item.name}</td>

                      <td>{item.slug}</td>

                      {product && (
                        <>
                          <td>
                            {item.category_name ||
                              "—"}

                            <br />

                            {item.brand_name ||
                              "—"}
                          </td>

                          <td>
                            {formatPrice(
                              item.price_sale,
                            )}

                            <br />

                            Tồn: {item.qty}
                          </td>
                          <td>
                            {Number(item.discount_percent) > 0 && item.final_price != null ? (
                              <>
                                <strong>{formatPrice(item.final_price)}</strong>
                                <br />
                                <small>Giảm {item.discount_percent}%</small>
                              </>
                            ) : "—"}
                          </td>
                        </>
                      )}

                      <td>
                        {Number(
                          item.status,
                        ) === 1
                          ? "Hoạt động"
                          : "Ẩn (" +
                            item.status +
                            ")"}
                      </td>

                      <td>
                        <div
                          className={
                            styles.actions
                          }
                        >
                          {trash ? (
                            <button
                              className={
                                styles.button
                              }
                              disabled={busy}
                              title="Khôi phục"
                              aria-label="Khôi phục"
                              onClick={() =>
                                mutate(
                                  resourcePath + "/" + item.id + "/restore",
                                  "PATCH",
                                  undefined,
                                  "Đã khôi phục thành công.",
                                )
                              }
                            >
                              <span
                                className={
                                  styles.actionIcon
                                }
                              >
                                <svg
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  strokeWidth="2"
                                  aria-hidden="true"
                                >
                                  <path d="M3 12a9 9 0 1 0 3-6.7" />
                                  <path d="M3 4v6h6" />
                                  <path d="M12 8v4l3 2" />
                                </svg>
                              </span>

                              <span
                                className={
                                  styles.actionText
                                }
                              >
                                Khôi phục
                              </span>
                            </button>
                          ) : (
                            <button
                              className={
                                styles.button
                              }
                              disabled={busy}
                              title="Sửa"
                              aria-label="Sửa"
                              onClick={() => {
                                setEditing(
                                  item,
                                );
                                setError("");
                                setNotice("");
                              }}
                            >
                              <span
                                className={
                                  styles.actionIcon
                                }
                              >
                                <svg
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  strokeWidth="2"
                                  aria-hidden="true"
                                >
                                  <path d="M12 20h9" />
                                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                </svg>
                              </span>

                              <span
                                className={
                                  styles.actionText
                                }
                              >
                                Sửa
                              </span>
                            </button>
                          )}

                          <button
                            className={
                              styles.dangerButton
                            }
                            disabled={busy}
                            title={
                              trash
                                ? "Xóa vĩnh viễn"
                                : "Xóa"
                            }
                            aria-label={
                              trash
                                ? "Xóa vĩnh viễn"
                                : "Xóa"
                            }
                            onClick={() =>
                              remove(item)
                            }
                          >
                            <span
                              className={
                                styles.actionIcon
                              }
                            >
                              <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2"
                                aria-hidden="true"
                              >
                                <path d="M3 6h18" />
                                <path d="M8 6V4h8v2" />
                                <path d="M19 6l-1 15H6L5 6" />
                                <path d="M10 11v6" />
                                <path d="M14 11v6" />
                              </svg>
                            </span>

                            <span
                              className={
                                styles.actionText
                              }
                            >
                              {trash
                                ? "Xóa vĩnh viễn"
                                : category
                                  ? "Xóa mềm"
                                  : "Xóa"}
                            </span>
                          </button>
                        </div>
                      </td>
                    </tr>
                  ),
                )}
              </tbody>
            </table>
          </div>

          {/* =================================================
              PAGINATION
          ================================================= */}

          {result.meta && (
            <nav
              className={
                styles.pagination
              }
              aria-label="Phân trang"
            >
              <button
                className={
                  styles.button
                }
                disabled={
                  busy || page <= 1
                }
                onClick={() => {
                  setEditing(null);
                  setPage(page - 1);
                }}
              >
                Trang trước
              </button>

              <span>
                Trang{" "}
                {
                  result.meta
                    .current_page
                }{" "}
                /{" "}
                {
                  result.meta
                    .last_page
                }
              </span>

              <button
                className={
                  styles.button
                }
                disabled={
                  busy ||
                  page >=
                    result.meta
                      .last_page
                }
                onClick={() => {
                  setEditing(null);
                  setPage(page + 1);
                }}
              >
                Trang sau
              </button>
            </nav>
          )}
        </>
      )}
    </div>
  );
}