"use client";
import Link from "next/link";
import { useState } from "react";
import { useCart } from "@/components/shop/CartProvider";

export default function ProfilePage() {
  const { user, loading, busy, error, updateProfile, logout } = useCart();
  const [notice, setNotice] = useState("");
  async function save(event) {
    event.preventDefault();
    setNotice("");
    if (await updateProfile(Object.fromEntries(new FormData(event.currentTarget)))) setNotice("Đã lưu hồ sơ.");
  }
  if (loading) return <p className="auth-page" role="status">Đang tải hồ sơ…</p>;
  if (!user) return <section className="auth-page"><h1>Hồ sơ cá nhân</h1>{error && <p role="alert">{error}</p>}<Link href="/login?next=/profile">Đăng nhập để xem hồ sơ</Link></section>;
  return <section className="auth-page">
    <p className="eyebrow">HH SHOP</p><h1>Hồ sơ cá nhân</h1>
    <p>Xin chào, {user.name}</p>
    {error && <p className="error-message" role="alert">{error}</p>}
    {notice && <p role="status">{notice}</p>}
    <form className="auth-form" onSubmit={save} key={user.id}>
      {[["name", "Họ và tên", "text"], ["username", "Tên đăng nhập", "text"], ["email", "Email", "email"], ["phone", "Số điện thoại", "tel"], ["address", "Địa chỉ", "text"]].map(([name,label,type]) => <div key={name}>
        <label htmlFor={name}>{label}</label><input id={name} name={name} type={type} defaultValue={user[name] || ""} required={name !== "address"} maxLength={255} disabled={busy}/>
      </div>)}
      <button className="primary-button" disabled={busy}>{busy ? "Đang xử lý…" : "Lưu thay đổi"}</button>
    </form>
    <div className="auth-links"><Link href="/orders">Đơn hàng của tôi</Link><button className="primary-button" type="button" disabled={busy} onClick={logout}>Đăng xuất</button></div>
  </section>;
}
