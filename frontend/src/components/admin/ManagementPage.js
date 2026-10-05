"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { formatPrice } from "@/utils/format";
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
          choices = { category_id: categories, brand_id: brands };
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
        if (active) setLoading(false);
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
      ? "Xóa vĩnh viễn danh mục " + item.name + "? Không thể khôi phục sau thao tác này."
      : "Xóa " + item.name + "?";

    if (window.confirm(message)) {
      mutate(
        trash ? "/categories/" + item.id + "/force" : endpoint + "/" + item.id,
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
              {trash ? "Danh sách danh mục" : "Thùng rác"}
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
              Thêm mới
            </button>
          )}
        </div>
      </div>
      {category && (
        <p className={styles.description}>
          API danh sách hiện chỉ hiển thị danh mục hoạt động. Danh mục ẩn sẽ không xuất
          hiện trong danh sách sau khi lưu hoặc khôi phục.
        </p>
      )}
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
              <caption>
                {title} — {result.meta?.total ?? result.data.length} bản ghi
              </caption>
              <thead>
                <tr>
                  <th>ID</th>
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
                    <td colSpan={product ? 7 : 5}>Chưa có dữ liệu.</td>
                  </tr>
                )}
                {result.data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.id}</td>
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
                            onClick={() =>
                              mutate(
                                "/categories/" + item.id + "/restore",
                                "PATCH",
                                undefined,
                                "Đã khôi phục danh mục.",
                              )
                            }
                          >
                            Khôi phục
                          </button>
                        ) : (
                          <button
                            className={styles.button}
                            disabled={busy}
                            onClick={() => {
                              setEditing(item);
                              setError("");
                              setNotice("");
                            }}
                          >
                            Sửa
                          </button>
                        )}
                        <button
                          className={styles.dangerButton}
                          disabled={busy}
                          onClick={() => remove(item)}
                        >
                          {trash ? "Xóa vĩnh viễn" : category ? "Xóa mềm" : "Xóa"}
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
                Trang {result.meta.current_page} / {result.meta.last_page}
              </span>
              <button
                className={styles.button}
                disabled={busy || page >= result.meta.last_page}
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
