"use client";
import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import styles from "../admin.module.css";
const labels = {about:"Giới thiệu", "purchase-policy":"Chính sách mua hàng", contact:"Liên hệ"};
export default function PagesAdmin() {
  const {token} = useAdminAuth();
  const [pages,setPages] = useState(null);
  const [selected,setSelected] = useState("about");
  const [error,setError] = useState("");
  const [notice,setNotice] = useState("");
  const [busy,setBusy] = useState(false);
  const [retry,setRetry] = useState(0);
  useEffect(() => {
    if (!token) return;
    let active=true;
    adminApi("/admin/pages", token).then(r => {if(active) setPages(r.data);}).catch(e => {if(active) setError(e.message);});
    return () => {active=false;};
  }, [token,retry]);
  async function save(event) {
    event.preventDefault();setBusy(true);setError("");setNotice("");
    const body=Object.fromEntries(new FormData(event.currentTarget));body.status=Number(body.status);
    try {
      const result=await adminApi("/admin/pages/"+selected,token,{method:"PUT",body});
      setPages(current => current.map(p => p.slug===selected ? result.data : p));
      setNotice(body.status===1 ? "Đã lưu và hiển thị trang trên cửa hàng." : "Đã lưu. Trang đang ẩn hoặc chưa xuất bản.");
    } catch(e) {setError(e.message);} finally {setBusy(false);}
  }
  const page=pages?.find(p => p.slug===selected);
  return <div><h1 className={styles.title}>Trang đơn</h1><p className={styles.description}>Chỉnh sửa nội dung và chọn trạng thái xuất bản cho từng trang.</p>
    {error && <p role="alert" className={styles.error}>{error}</p>}{notice && <p role="status">{notice}</p>}
    {!pages ? error ? <button className={styles.button} onClick={() => {setError("");setRetry(retry+1);}}>Thử lại</button> : <p role="status">Đang tải trang…</p> : <>
      <div className={styles.actions}>{Object.entries(labels).map(([slug,label]) => <button key={slug} className={slug===selected ? styles.primaryButton : styles.button} disabled={busy} aria-pressed={slug===selected} onClick={() => {setSelected(slug);setNotice("");setError("");}}>{label}</button>)}</div>
      <form onSubmit={save} className={styles.form} key={selected}>
        <fieldset disabled={busy} style={{border:0,padding:0,marginTop:24,display:"grid",gap:12}}>
          <label htmlFor="page-title">Tiêu đề</label><input id="page-title" name="title" defaultValue={page.title} maxLength={255} required/>
          <label htmlFor="page-description">Mô tả ngắn</label><textarea id="page-description" name="description" defaultValue={page.description || ""} maxLength={2000} rows={3}/>
          <label htmlFor="page-detail">Nội dung</label><textarea id="page-detail" name="detail" defaultValue={page.detail} maxLength={100000} rows={16} required aria-describedby="content-help"/>
          <small id="content-help">Nhập văn bản, xuống dòng để chia đoạn. Nội dung hiển thị dưới dạng văn bản thuần.</small>
          <label htmlFor="page-status">Trạng thái</label><select id="page-status" name="status" defaultValue={page.status}><option value="2">Bản nháp</option><option value="1">Hiển thị</option><option value="0">Ẩn</option></select>
          <div className={styles.actions}><button className={styles.primaryButton}>{busy ? "Đang lưu…" : "Lưu trang"}</button>{Number(page.status)===1 && <Link href={"/"+selected} target="_blank" rel="noopener noreferrer" className={styles.button}>Xem trang đã lưu</Link>}</div>
        </fieldset>
      </form>
    </>}
  </div>;
}
