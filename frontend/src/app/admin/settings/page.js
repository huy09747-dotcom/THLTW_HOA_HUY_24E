"use client";
import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import styles from "../admin.module.css";
const fields = [["site_name","Tên website","text"],["contact_email","Email liên hệ","email"],["contact_phone","Điện thoại liên hệ","tel"],["address","Địa chỉ","text"],["logo","URL logo (http/https)","url"],["favicon","URL favicon (http/https)","url"],["facebook","Facebook","url"],["instagram","Instagram","url"],["youtube","YouTube","url"],["seo_title","Tiêu đề SEO","text"],["seo_keywords","Từ khóa SEO","text"],["meta_description","Mô tả website (SEO)","textarea"]];
export default function SettingsAdmin() {
  const {token}=useAdminAuth();
  const [settings,setSettings]=useState(null);
  const [error,setError]=useState("");const [notice,setNotice]=useState("");const [busy,setBusy]=useState(false);const [retry,setRetry]=useState(0);
  useEffect(() => {if(!token)return;let active=true;
    adminApi("/admin/site-settings",token).then(r => {if(active)setSettings(r.data);}).catch(e => {if(active)setError(e.message);});
    return () => {active=false;};
  },[token,retry]);
  async function save(event) {
    event.preventDefault();setBusy(true);setError("");setNotice("");
    const body=Object.fromEntries(new FormData(event.currentTarget));
    try {const r=await adminApi("/admin/site-settings",token,{method:"PUT",body});setSettings(r.data);setNotice("Đã lưu cấu hình. Tải lại cửa hàng để xem thay đổi.");}
    catch(e){setError(e.message);}finally{setBusy(false);}
  }
  return <div><h1 className={styles.title}>Cấu hình website</h1><p className={styles.description}>Thông tin dùng chung cho đầu trang, chân trang và trang Liên hệ.</p>
    {error && <p role="alert" className={styles.error}>{error}</p>}{notice && <p role="status">{notice}</p>}
    {!settings ? error ? <button className={styles.button} onClick={() => {setError("");setRetry(retry+1);}}>Thử lại</button> : <p role="status">Đang tải cấu hình…</p> : <form onSubmit={save} className={styles.form}><fieldset disabled={busy} style={{border:0,padding:0,display:"grid",gap:12}}>
      {fields.map(([name,label,type]) => <div key={name}><label htmlFor={name}>{label}</label>{type==="textarea" ? <textarea id={name} name={name} defaultValue={settings[name] || ""} rows={4} maxLength={500}/> : <input id={name} name={name} type={type} defaultValue={settings[name] || ""} maxLength={name==="contact_phone" ? 20 : name==="contact_email" ? 150 : 255} required={name==="site_name"}/>}</div>)}
      <button className={styles.primaryButton}>{busy ? "Đang lưu…" : "Lưu cấu hình"}</button>
    </fieldset></form>}
  </div>;
}
