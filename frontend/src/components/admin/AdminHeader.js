"use client";

import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import styles from "@/app/admin/admin.module.css";

export default function AdminHeader() {
  const { user, busy, logout } = useAdminAuth();

  return (
    <header className={styles.header}>
      <div>
        <p className={styles.caption}>HH SHOP ADMIN</p>
        <strong>Không gian quản trị</strong>
      </div>
      <div className={styles.account}>
        <span>{user.name}</span>
        <button
          type="button"
          onClick={logout}
          disabled={busy}
          className={styles.button}
        >
          {busy ? "Đang đăng xuất…" : "Đăng xuất"}
        </button>
      </div>
    </header>
  );
}
