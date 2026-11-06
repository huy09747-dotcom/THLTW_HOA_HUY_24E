"use client";
import { useState } from "react";
import { api } from "@/services/api";
import { useCart } from "./CartProvider";
export default function ContactForm() {
  const { user } = useCart();
  const [busy, setBusy] = useState(false);
  const [state, setState] = useState({});
  return <form className="feature-panel feature-form" onSubmit={async e => {
    e.preventDefault(); const form=e.currentTarget; setBusy(true); setState({});
    try { const result=await api("/contacts",{method:"POST",body:Object.fromEntries(new FormData(form))}); setState({message:result.message}); form.reset(); }
    catch(error) { setState({error:error.message}); } finally { setBusy(false); }
  }}>
    <h2>Gửi yêu cầu hỗ trợ</h2>
    {[["name","Họ tên","text"],["email","Email","email"],["phone","Điện thoại","tel"],["title","Tiêu đề","text"]].map(([name,label,type]) => <label key={name}>{label}<input name={name} type={type} required maxLength={255} defaultValue={user?.[name] || ""} disabled={busy} /></label>)}
    <label className="feature-full">Nội dung<textarea name="content" required maxLength={10000} rows={5} disabled={busy} /></label>
    <div className="feature-actions"><button disabled={busy}>{busy?"Đang gửi…":"Gửi liên hệ"}</button></div>
    {state.message && <p className="feature-full" role="status">{state.message}</p>}{state.error && <p role="alert" className="error-message feature-full">{state.error}</p>}
  </form>;
}
