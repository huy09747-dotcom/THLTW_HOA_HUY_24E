"use client";
import { useState } from "react";
import { useCart } from "./CartProvider";
import { api } from "@/services/api";
export default function ChangePassword() {
  const { token }=useCart();
  const [busy,setBusy]=useState(false);
  const [state,setState]=useState({});
  return <form className="feature-panel feature-form" onSubmit={async e=>{
    e.preventDefault(); const form=e.currentTarget; setBusy(true); setState({});
    try { const r=await api('/profile/password',{method:'PUT',token,body:Object.fromEntries(new FormData(form))});setState({message:r.message});form.reset(); }
    catch(error){setState({error:error.message});} finally{setBusy(false);}
  }}>
    <h2>Đổi mật khẩu</h2>
    {[["current_password","Mật khẩu hiện tại"],["password","Mật khẩu mới"],["password_confirmation","Xác nhận mật khẩu mới"]].map(([name,label])=><label key={name}>{label}<input name={name} type="password" required minLength={name==='current_password'?undefined:8} maxLength={255} autoComplete={name==='current_password'?'current-password':'new-password'} disabled={busy}/></label>)}
    <div className="feature-actions"><button disabled={busy}>{busy?'Đang lưu…':'Đổi mật khẩu'}</button></div>
    {(state.message||state.error)&&<p className="feature-full" role={state.error?'alert':'status'}>{state.error||state.message}</p>}
  </form>;
}
