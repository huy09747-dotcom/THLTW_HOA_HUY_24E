"use client";

import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { formatPrice } from "@/utils/format";
import styles from "../admin.module.css";

function localDate(value) {
  if (!value) return "";
  const date = new Date(value.replace(" ", "T") + "Z");
  if (Number.isNaN(date.getTime())) return "";
  return new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
}

const blank = { code: "", type: "percent", value: "", min_order: 0, max_discount: "", usage_limit: "", starts_at: "", ends_at: "", active: true };
const fields = [
  ["code", "Mã giảm giá", "text", true],
  ["value", "Giá trị giảm (% hoặc đồng)", "number", true],
  ["min_order", "Giá trị đơn tối thiểu (đ)", "number", true],
  ["max_discount", "Giảm tối đa (đ, bỏ trống nếu không giới hạn)", "number"],
  ["usage_limit", "Tổng lượt sử dụng tối đa (bỏ trống nếu không giới hạn)", "number"],
  ["starts_at", "Bắt đầu", "datetime-local"],
  ["ends_at", "Kết thúc", "datetime-local"],
];

export default function CouponsPage() {
  const { token } = useAdminAuth();
  const [query,setQuery] = useState("");
  const [page, setPage] = useState(1);
  const [retry, setRetry] = useState(0);
  const [state, setState] = useState({});
  const [form, setForm] = useState(blank);
  const [editing, setEditing] = useState(null);
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  useEffect(() => {
    if (!token) return;
    let active = true;
    adminApi(`/admin/coupons?page=${page}&${query}`, token).then(result => {
      if (active) setState({ result, page, retry });
    }).catch(e => { if (active) setState({ error: e.message, page, retry }); });
    return () => { active = false; };
  }, [token, page, retry, query]);

  async function save(event) {
    event.preventDefault();
    setBusy(true); setError(""); setMessage("");
    try {
      const body = Object.fromEntries(Object.entries(form).map(([key, value]) => [key, value === "" ? null : value]));
      for (const key of ["starts_at", "ends_at"]) {
        if (body[key]) body[key] = new Date(body[key]).toISOString();
      }
      await adminApi(`/admin/coupons${editing ? `/${editing}` : ""}`, token, { method: editing ? "PUT" : "POST", body });
      setForm(blank); setEditing(null); setPage(1); setRetry(n => n + 1); setMessage("Đã lưu mã giảm giá.");
    } catch (e) { setError(e.message); }
    finally { setBusy(false); }
  }
  function edit(item) {
    const next = {};
    for (const key of Object.keys(blank)) next[key] = item[key] ?? "";
    next.active = Boolean(item.active);
    next.starts_at = localDate(next.starts_at);
    next.ends_at = localDate(next.ends_at);
    setForm(next); setEditing(item.id); setError(""); setMessage("");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
  const pending = state.page !== page || state.retry !== retry;
  return (
    <section>
      <h1 className={styles.title}>Mã giảm giá</h1>
      <form className="feature-panel feature-form" onSubmit={save}>
        <h2>{editing ? "Chỉnh sửa mã" : "Tạo mã giảm giá"}</h2>
        <label>Loại giảm<select value={form.type} onChange={e => setForm({ ...form, type: e.target.value })} disabled={busy}>
          <option value="percent">Phần trăm (%)</option><option value="fixed">Số tiền cố định (đ)</option>
        </select></label>
        {fields.map(([name, label, type, required]) => <label key={name}>{label}
          <input type={type} value={form[name]} required={required} disabled={busy}
            maxLength={name === "code" ? 40 : undefined} min={type === "number" ? name === "min_order" ? 0 : 1 : undefined}
            max={name === "value" && form.type === "percent" ? 100 : undefined}
            onChange={e => setForm({ ...form, [name]: name === "code" ? e.target.value.toUpperCase() : e.target.value })} />
        </label>)}
        <label className="feature-check"><input type="checkbox" checked={form.active} disabled={busy} onChange={e => setForm({ ...form, active: e.target.checked })} />Đang hoạt động</label>
        <div className="feature-actions"><button className={styles.primaryButton} disabled={busy}>{busy ? "Đang lưu…" : "Lưu mã"}</button>
          {editing && <button type="button" disabled={busy} onClick={() => { setEditing(null); setForm(blank); }}>Hủy sửa</button>}
        </div>
      </form>
      <p className={styles.description}>Một đơn áp dụng một mã. Lượt được tính khi đặt đơn thành công, kể cả đơn hủy. Bỏ chọn hoạt động để ngừng mã.</p>
      {message && <p role="status">{message}</p>}{(error || state.error) && <p role="alert" className="error-message">{error || state.error}</p>}
      <form className="feature-panel feature-actions" onSubmit={e=>{e.preventDefault();setPage(1);setQuery(new URLSearchParams(new FormData(e.currentTarget)).toString());setRetry(n=>n+1);}}><input name="search" placeholder="Tìm mã" aria-label="Tìm mã" maxLength={255}/><select name="status" aria-label="Hoạt động"><option value="">Tất cả</option><option value="1">Bật</option><option value="0">Tắt</option></select><button>Lọc</button></form>
      {pending ? <p role="status">Đang tải…</p> : state.result && <>
        <div className="feature-panel feature-table"><table className={styles.table}><thead><tr><th>Mã</th><th>Giảm</th><th>Đơn tối thiểu</th><th>Đã dùng / Giới hạn</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
          {state.result.data.map(item => <tr key={item.id}><td><strong>{item.code}</strong></td><td>{item.type === "percent" ? `${item.value}%` : formatPrice(item.value)}{item.max_discount && <small> · Tối đa {formatPrice(item.max_discount)}</small>}</td><td>{formatPrice(item.min_order)}</td><td>{item.used} / {item.usage_limit ?? "∞"}</td><td>{item.active ? "Bật" : "Tắt"}<small>{item.ends_at ? ` · Hết hạn: ${new Date(item.ends_at.replace(" ", "T") + "Z").toLocaleString("vi-VN")}` : ""}</small></td><td><button className={styles.button} disabled={busy} onClick={() => edit(item)}>Sửa</button><button className={styles.dangerButton} disabled={busy} onClick={async()=>{
            if(!window.confirm(`Xóa mã ${item.code}?`))return;setBusy(true);setError("");
            try{await adminApi(`/admin/coupons/${item.id}`,token,{method:"DELETE"});setRetry(n=>n+1);}catch(e){setError(e.message);}finally{setBusy(false);}
          }}>Xóa</button></td></tr>)}
          {!state.result.data.length && <tr><td colSpan={6}>Chưa có mã giảm giá.</td></tr>}
        </tbody></table></div>
        <div className="feature-actions"><button disabled={page <= 1} onClick={() => setPage(n => n - 1)}>Trang trước</button><span>{page} / {state.result.last_page}</span><button disabled={page >= state.result.last_page} onClick={() => setPage(n => n + 1)}>Trang sau</button></div>
      </>}
    </section>
  );
}
