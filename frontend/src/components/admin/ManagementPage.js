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

  for (let page = 2; page <= (first.meta?.last_page || 1); page++) {
    const next = await adminApi(path + "?page=" + page, token);
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

  useEffect(() => {
    let active = true;

    async function load() {
      setLoading(true);
      setError("");

      try {
        const data = await adminApi(endpoint + "?page=" + page, token);
        let choices = {};

        if (product) {
          const [categories, brands] = await Promise.all([
            allOptions("/categories", token),
            allOptions("/brands", token),
          ]);

          choices = {
            category_id: categories,
            brand_id: brands,
          };
        }

        if (active) {
          const lastPage = data.meta?.last_page || 1;

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
  }, [endpoint, token, page, revision, product]);

  async function mutate(path, method, body, message) {
    setBusy(true);
    setError("");
    setNotice("");

    try {
      await adminApi(path, token, { method, body });
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
      editing.id ? endpoint + "/" + editing.id : endpoint,
      editing.id ? "PUT" : "POST",
      body,
      "Đã lưu thành công.",
    );
  }

  function remove(item) {
    const message = trash
      ? "Xóa vĩnh viễn danh mục " +
        item.name +
        "? Không thể khôi phục sau thao tác này."
      : "Xóa " + item.name + "?";

    if (window.confirm(message)) {
      mutate(
        trash
          ? "/categories/" + item.id + "/force"
          : endpoint + "/" + item.id,
        "DELETE",
        undefined,
        trash ? "Đã xóa vĩnh viễn." : "Đã xóa thành công.",
      );
    }
  }

  return (
    <div>
      <div className={styles.toolbar}>
        <h1 className={styles.title}>{title}</h1>

        <div className={styles.actions}>
          {category && (
            <Link
              href={trash ? "/admin/categories" : "/admin/categories/trash"}
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
                {trash ? "Danh sách danh mục" : "Thùng rác"}
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

      {error && (
        <p role="alert" className={styles.error}>
          {error}
        </p>
      )}

      {notice && (
        <p role="status" className={styles.success}>
          {notice}
        </p>
      )}

      {editing && (
        <Form
          key={editing.id || "new"}
          initial={editing}
          options={options}
          busy={busy}
          onSave={save}
          onCancel={() => setEditing(null)}
        />
      )}

      {loading ? (
        <p role="status">Đang tải dữ liệu…</p>
      ) : !result ? (
        <button
          className={styles.button}
          onClick={() => setRevision((value) => value + 1)}
        >
          Thử lại
        </button>
      ) : (
        <>
          <div className={styles.tableWrap}>
            <table className={styles.table}>
              <caption className="sr-only">{title}</caption>

              <thead>
                <tr>
                  <th>ID</th>
                  <th>Hình ảnh</th>
                  <th>Tên</th>
                  <th>Slug</th>

                  {product && (
                    <>
                      <th>Danh mục / Thương hiệu</th>
                      <th>Giá bán / Tồn</th>
                    </>
                  )}

                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
              </thead>

              <tbody>
                {result.data.length === 0 && (
                  <tr>
                    <td colSpan={product ? 8 : 6}>
                      Chưa có dữ liệu.
                    </td>
                  </tr>
                )}

                {result.data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.id}</td>

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
                          {item.category_name || "—"}
                          <br />
                          {item.brand_name || "—"}
                        </td>

                        <td>
                          {formatPrice(item.price_sale)}
                          <br />
                          Tồn: {item.qty}
                        </td>
                      </>
                    )}

                    <td>
                      {Number(item.status) === 1
                        ? "Hoạt động"
                        : "Ẩn (" + item.status + ")"}
                    </td>

                    <td>
                      <div className={styles.actions}>
                        {trash ? (
                          <button
                            className={styles.button}
                            disabled={busy}
                            title="Khôi phục"
                            aria-label="Khôi phục"
                            onClick={() =>
                              mutate(
                                "/categories/" +
                                  item.id +
                                  "/restore",
                                "PATCH",
                                undefined,
                                "Đã khôi phục danh mục.",
                              )
                            }
                          >
                            <span className={styles.actionIcon}>
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
                            <span className={styles.actionText}>
                              Khôi phục
                            </span>
                          </button>
                        ) : (
                          <button
                            className={styles.button}
                            disabled={busy}
                            title="Sửa"
                            aria-label="Sửa"
                            onClick={() => {
                              setEditing(item);
                              setError("");
                              setNotice("");
                            }}
                          >
                            <span className={styles.actionIcon}>
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
                            <span className={styles.actionText}>
                              Sửa
                            </span>
                          </button>
                        )}

                        <button
                          className={styles.dangerButton}
                          disabled={busy}
                          title={trash ? "Xóa vĩnh viễn" : "Xóa"}
                          aria-label={
                            trash ? "Xóa vĩnh viễn" : "Xóa"
                          }
                          onClick={() => remove(item)}
                        >
                          <span className={styles.actionIcon}>
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

                          <span className={styles.actionText}>
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
                ))}
              </tbody>
            </table>
          </div>

          {result.meta && (
            <nav
              className={styles.pagination}
              aria-label="Phân trang"
            >
              <button
                className={styles.button}
                disabled={busy || page <= 1}
                onClick={() => {
                  setEditing(null);
                  setPage(page - 1);
                }}
              >
                Trang trước
              </button>

              <span>
                Trang {result.meta.current_page} /{" "}
                {result.meta.last_page}
              </span>

              <button
                className={styles.button}
                disabled={
                  busy || page >= result.meta.last_page
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