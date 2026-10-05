"use client";

import Link from "next/link";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import styles from "../admin.module.css";

export default function AdminLoginPage() {
  const { login, busy, error } = useAdminAuth();

  async function handleSubmit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const data = new FormData(form);
    const success = await login(data.get("email"), data.get("password"));

    if (success) {
      form.reset();
    }
  }

  return (
    <section className={styles.loginCard}>
      <p className={styles.caption}>HH SHOP ADMIN</p>
      <h1 className={styles.title}>Đăng nhập quản trị</h1>
      <p className={styles.description}>Đăng nhập để quản lý HH Shop.</p>
      {error && (
        <p
          role="alert"
          className={styles.error}
        >
          {error}
        </p>
      )}
<form
        onSubmit={handleSubmit}
        className={styles.form}
      >
        <label htmlFor="admin-email">Email</label>
        <input
          id="admin-email"
          name="email"
          type="email"
          autoComplete="username"
          required
          disabled={busy}
        />
        <label htmlFor="admin-password">Mật khẩu</label>
        <input
          id="admin-password"
          name="password"
          type="password"
          autoComplete="current-password"
          required
          disabled={busy}
        />
        <button
          type="submit"
          className={styles.primaryButton}
          disabled={busy}
        >
          {busy ? "Đang đăng nhập…" : "Đăng nhập"}
        </button>
      </form>
      <Link
        href="/"
        className={styles.backLink}
      >
        ← Về cửa hàng
      </Link>
    </section>
  );
}
