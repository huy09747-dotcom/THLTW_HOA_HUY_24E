"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { useAdminAuth } from "./AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { toSlug } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

export default function CrudScreen({ title, endpoint, fields, columns, allowCreate = true, statusOptions = [[1,"Hiển thị"],[0,"Ẩn"]], pageLinks = false }) {
  const { token } = useAdminAuth();
  const [page, setPage] = useState(1);
  const [query, setQuery] = useState("");
  const [revision, setRevision] = useState(0);
  const [state, setState] = useState({});
  const [editing, setEditing] = useState(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  useEffect(() => {
    if (!token) return;
    let active = true;
    adminApi(`${endpoint}?page=${page}&${query}`, token).then(result => {
      if (!active) return;
      const meta = result.meta || result;
      if (page > meta.last_page) setPage(meta.last_page);
      else setState({ result, page, query, revision });
    }).catch(e => { if (active) setState({ error: e.message, page, query, revision }); });
    return () => { active = false; };
  }, [token, endpoint, page, query, revision]);

  async function mutate(action) {
    setBusy(true); setError(""); setNotice("");
    try {
      const result = await action();
      setNotice(result.message || "Đã xử lý thành công."); setEditing(null); setRevision(n => n + 1);
    } catch (e) { setError(e.message); }
    finally { setBusy(false); }
  }
  const pending = state.page !== page || state.query !== query || state.revision !== revision;
  const data = state.result;
  const meta = data?.meta || data;
  return (
    <section>
      <div className={styles.toolbar}><h1 className={styles.title}>{title}</h1>
        {allowCreate && <button className={styles.primaryButton} disabled={busy} onClick={() => setEditing({})}>Thêm mới</button>}
      </div>
      <form className="feature-panel feature-actions" onSubmit={e => {
        e.preventDefault(); setPage(1); setQuery(new URLSearchParams(new FormData(e.currentTarget)).toString()); setRevision(n => n + 1);
      }}>
        <input name="search" placeholder="Tìm kiếm…" maxLength={255} aria-label="Tìm kiếm" />
        <select name="status" aria-label="Lọc trạng thái"><option value="">Tất cả trạng thái</option>{statusOptions.map(([value,label]) => <option key={value} value={value}>{label}</option>)}</select>
        <button disabled={busy}>Tìm kiếm / Lọc</button>
      </form>
      {error && <p className={styles.error} role="alert">{error}</p>}
      {notice && <p role="status">{notice}</p>}
      {editing && <form className="feature-panel feature-form" onSubmit={e => {
        e.preventDefault(); const body = Object.fromEntries(new FormData(e.currentTarget));
        mutate(() => adminApi(endpoint + (editing.id ? `/${editing.id}` : ""), token, { method: editing.id ? "PUT" : "POST", body }));
      }} key={editing.id || "new"}>
        <h2>{editing.id ? "Chỉnh sửa / Chi tiết" : "Thêm mới"}</h2>
        {fields.map(field => <label key={field.name} className={field.type === "textarea" ? "feature-full" : ""}>{field.label}
          {field.type === "textarea" ? <textarea name={field.name} defaultValue={editing[field.name] || ""} required={field.required} maxLength={field.max || 100000} rows={field.rows || 5} disabled={busy || field.readOnly} />
            : field.options ? <select name={field.name} defaultValue={editing[field.name] ?? field.default ?? ""} disabled={busy || field.readOnly}>{field.options.map(([value,label]) => <option key={value} value={value}>{label}</option>)}</select>
              : <input name={field.name} type={field.type || "text"} defaultValue={editing[field.name] ?? field.default ?? ""} required={field.required} maxLength={field.max || 255} min={field.type === "number" ? 0 : undefined} disabled={busy || field.readOnly}
                onChange={field.name === "name" || field.name === "title" ? e => {
                  const slug = e.target.form.elements.namedItem("slug"); if (slug && !editing.id) slug.value = toSlug(e.target.value);
                } : undefined} />}
        </label>)}
        <div className="feature-actions"><button className={styles.primaryButton} disabled={busy}>{busy ? "Đang lưu…" : "Lưu"}</button><button type="button" disabled={busy} onClick={() => setEditing(null)}>Đóng</button></div>
      </form>}
      {pending ? <p role="status">Đang tải…</p> : state.error ? <p role="alert">{state.error}</p> : data && <>
        <div className={styles.tableWrap}><table className={styles.table}><thead><tr>{columns.map(column => <th key={column.name}>{column.label}</th>)}<th>Thao tác</th></tr></thead><tbody>
          {data.data.map(item => <tr key={item.id}>{columns.map(column => <td key={column.name} style={{ maxWidth: 350, whiteSpace: "pre-wrap", overflowWrap: "anywhere" }}>{column.name === "status" ? (statusOptions.find(([value]) => Number(value) === Number(item.status))?.[1] || item.status) : String(item[column.name] ?? "—")}</td>)}
            <td><div className={styles.actions}><button className={styles.button} disabled={busy} onClick={() => setEditing(item)}>Chi tiết / Sửa</button>
              {pageLinks && <Link className={styles.button} href={`/pages/${item.slug}`} target="_blank">Xem trang</Link>}
              <button className={styles.dangerButton} disabled={busy} onClick={() => {
                if (window.confirm("Xóa bản ghi này?")) mutate(() => adminApi(`${endpoint}/${item.id}`, token, { method: "DELETE" }));
              }}>Xóa</button></div></td>
          </tr>)}
          {!data.data.length && <tr><td colSpan={columns.length + 1}>Chưa có dữ liệu.</td></tr>}
        </tbody></table></div>
        <div className={styles.pagination}><button className={styles.button} disabled={busy || page <= 1} onClick={() => setPage(n => n - 1)}>Trang trước</button><span>{page} / {meta.last_page}</span><button className={styles.button} disabled={busy || page >= meta.last_page} onClick={() => setPage(n => n + 1)}>Trang sau</button></div>
      </>}
    </section>
  );
}
