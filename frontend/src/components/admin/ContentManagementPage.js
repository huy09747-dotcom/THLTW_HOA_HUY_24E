"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import PostForm from "./PostForm";
import MenuForm from "./MenuForm";
import AdminImage from "./AdminImage";
import * as postServices from "@/services/postServices";
import * as menuServices from "@/services/menuServices";
import styles from "@/app/admin/admin.module.css";

const menuTypes = {
  custom: "Tùy chỉnh",
  category: "Danh mục",
  brand: "Thương hiệu",
  topic: "Chủ đề",
  page: "Trang đơn",
};

export default function ContentManagementPage({ kind, trash = false }) {
  const { token } = useAdminAuth();
  const isPost = kind === "post";
  const service = isPost ? postServices : menuServices;
  const title = isPost ? "bài viết" : "menu";
  const path = `/admin/${isPost ? "posts" : "menus"}`;
  const Form = isPost ? PostForm : MenuForm;
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState("");
  const [search, setSearch] = useState("");
  const [query, setQuery] = useState("");
  const [revision, setRevision] = useState(0);
  const [result, setResult] = useState(null);
  const [options, setOptions] = useState({});
  const [editing, setEditing] = useState(null);
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!token) return;
    let active = true;

    async function load() {
      try {
        const [data, choices] = await Promise.all([
          service.getList(page, query, trash, token, status),
          trash ? Promise.resolve({}) : service.getOptions(token),
        ]);
        if (!active) return;
        if (page > data.meta.last_page) {
          setPage(data.meta.last_page);
          return;
        }
        setResult(data);
        setOptions(choices);
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
    return () => { active = false; };
  }, [service, page, query, revision, trash, token, status]);

  async function mutate(action) {
    setBusy(true);
    setError("");
    try {
      await action();
      setEditing(null);
      setLoading(true);
      setRevision((value) => value + 1);
    } catch (exception) {
      setError(exception.message);
    } finally {
      setBusy(false);
    }
  }

  function changePage(next) {
    setEditing(null);
    setError("");
    setLoading(true);
    setPage(next);
  }

  function remove(item) {
    const name = isPost ? item.title : item.name;
    const message = trash ? `Xóa vĩnh viễn ${name}? Không thể khôi phục.` : `Chuyển ${name} vào thùng rác?`;
    if (window.confirm(message)) {
      mutate(() => service.removeItem(item.id, trash, token));
    }
  }

  return (
    <div>
      <div className={styles.toolbar}>
        <h1 className={styles.title}>{trash ? "Thùng rác" : "Quản lý"} {title}</h1>
        <div className={styles.actions}>
          <Link className={styles.button} href={trash ? path : `${path}/trash`}>
            {trash ? "Về danh sách" : "Thùng rác"}
          </Link>
          {!trash && (
            <button type="button" className={styles.primaryButton} disabled={loading || busy} onClick={() => setEditing({})}>
              Thêm mới
            </button>
          )}
        </div>
      </div>
      {!trash && (
        <form
          className={styles.filterBox}
          onSubmit={(event) => {
            event.preventDefault();
            setPage(1);
            setQuery(search.trim());
            setRevision((value) => value + 1);
            setLoading(true);
            setEditing(null);
            setError("");
          }}
        >
          <div className={styles.filterField}>
            <label htmlFor="content-search">Tìm kiếm</label>
            <input id="content-search" value={search} maxLength={255} onChange={(event) => setSearch(event.target.value)} />
          </div>
          <div className={styles.filterField}><label htmlFor="content-status">Trạng thái</label><select id="content-status" value={status} onChange={e=>{setStatus(e.target.value);setPage(1);}}><option value="">Tất cả</option><option value="1">Hiển thị</option><option value="0">Ẩn</option><option value="2">Bản nháp</option></select></div>
          <div className={styles.filterActions}>
            <button type="submit" className={styles.primaryButton} disabled={busy || loading}>Tìm kiếm</button>
          </div>
        </form>
      )}
      {error && <p className={styles.error} role="alert">{error}</p>}
      {editing && !trash && (
        <Form
          key={editing.id || "new"}
          initial={editing}
          options={options}
          busy={busy}
          onCancel={() => setEditing(null)}
          onSave={(body) => mutate(() => service.saveItem(editing.id, body, token))}
        />
      )}
      {loading ? (
        <p role="status">Đang tải...</p>
      ) : result && (
        <>
          <div className={styles.tableWrap}>
            <table className={styles.table}>
              <thead>
                <tr>
                  <th>STT</th>
                  {isPost && <th>Hình ảnh</th>}
                  <th>{isPost ? "Tiêu đề" : "Tên menu"}</th>
                  <th>{isPost ? "Slug" : "Đường dẫn"}</th>
                  <th>{isPost ? "Chủ đề" : "Loại menu"}</th>
                  {!isPost && <th>Thứ tự</th>}
                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                {result.data.map((item, index) => (
                  <tr key={item.id}>
                    <td>{(result.meta.from ?? 1) + index}</td>
                    {isPost && <td><AdminImage image={item.image} name={item.title} /></td>}
                    <td>{isPost ? item.title : item.name}</td>
                    <td>{isPost ? item.slug : item.link}</td>
                    <td>{isPost ? item.topic_name || "—" : menuTypes[item.type]}</td>
                    {!isPost && <td>{item.sort_order}</td>}
                    <td>{Number(item.status) === 1 ? "Hoạt động" : Number(item.status) === 2 ? "Chưa xuất bản" : "Ẩn"}</td>
                    <td>
                      <div className={styles.actions}>
                        {trash ? (
                          <button type="button" className={styles.button} disabled={busy} onClick={() => mutate(() => service.restoreItem(item.id, token))}>
                            Khôi phục
                          </button>
                        ) : (
                          <button type="button" className={styles.button} disabled={busy} onClick={() => { setEditing(item); setError(""); }}>
                            Sửa
                          </button>
                        )}
                        <button type="button" className={styles.dangerButton} disabled={busy} onClick={() => remove(item)}>
                          {trash ? "Xóa vĩnh viễn" : "Xóa mềm"}
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
                {result.data.length === 0 && <tr><td colSpan={7}>Chưa có {title}.</td></tr>}
              </tbody>
            </table>
          </div>
          <nav className={styles.pagination} aria-label="Phân trang">
            <button type="button" className={styles.button} disabled={busy || page <= 1} onClick={() => changePage(page - 1)}>
              Trang trước
            </button>
            <span>Trang {result.meta.current_page} / {result.meta.last_page}</span>
            <button type="button" className={styles.button} disabled={busy || page >= result.meta.last_page} onClick={() => changePage(page + 1)}>
              Trang sau
            </button>
          </nav>
        </>
      )}
    </div>
  );
}
