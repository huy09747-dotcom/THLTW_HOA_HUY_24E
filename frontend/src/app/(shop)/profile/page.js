"use client";

import Link from "next/link";
import { useState } from "react";
import { useCart } from "@/components/shop/CartProvider";
import styles from "./profile.module.css";

const fields = [
  ["name", "Họ và tên", "text", "name"],
  ["username", "Tên đăng nhập", "text", "username"],
  ["email", "Email", "email", "email"],
  ["phone", "Số điện thoại", "tel", "tel"],
];

export default function ProfilePage() {
  const { user, loading, busy, error, updateProfile, logout } = useCart();
  const [notice, setNotice] = useState("");

  async function save(event) {
    event.preventDefault();
    setNotice("");
    const values = Object.fromEntries(new FormData(event.currentTarget));
    if (await updateProfile(values)) setNotice("Đã lưu thông tin cá nhân.");
  }

  if (loading) return <p className="auth-page" role="status">Đang tải hồ sơ…</p>;
  if (!user) {
    return (
      <section className="auth-page">
        <h1>Hồ sơ cá nhân</h1>
        {error && <p role="alert">{error}</p>}
        <Link className={styles.secondary} href="/login?next=/profile">
          Đăng nhập để xem hồ sơ
        </Link>
      </section>
    );
  }

  return (
    <section className={styles.page}>
      <header className={styles.header}>
        <div className={styles.avatar} aria-hidden="true">
          {user.name?.trim().charAt(0).toUpperCase() || "HH"}
        </div>
        <div>
          <p className="eyebrow">TÀI KHOẢN HH SHOP</p>
          <h1>Hồ sơ cá nhân</h1>
          <p className={styles.greeting}>{user.name}</p>
        </div>
      </header>
      <div className={styles.content}>
        <h2>Thông tin cá nhân</h2>
        {error && <p className="error-message" role="alert">{error}</p>}
        {notice && <p className={styles.success} role="status">✓ {notice}</p>}
        <form className={styles.form} onSubmit={save} key={user.id}>
          {fields.map(([name, label, type, autoComplete]) => (
            <div className={styles.field} key={name}>
              <label htmlFor={name}>{label}</label>
              <input
                id={name}
                name={name}
                type={type}
                autoComplete={autoComplete}
                defaultValue={user[name] || ""}
                required
                maxLength={255}
                disabled={busy}
              />
            </div>
          ))}
          <div className={`${styles.field} ${styles.full}`}>
            <label htmlFor="address">Địa chỉ</label>
            <textarea
              id="address"
              name="address"
              autoComplete="street-address"
              defaultValue={user.address || ""}
              maxLength={255}
              rows={2}
              disabled={busy}
            />
          </div>
          <div className={styles.actions}>
            <button className={styles.primary} disabled={busy} type="submit">
              {busy ? "Đang xử lý…" : "Lưu thay đổi"}
            </button>
            <button className={styles.secondary} type="button" disabled={busy} onClick={logout}>
              Đăng xuất
            </button>
          </div>
        </form>
      </div>
    </section>
  );
}
