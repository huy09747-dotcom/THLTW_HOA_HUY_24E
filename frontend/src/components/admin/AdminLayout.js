"use client";

import { useEffect } from "react";
import { usePathname, useRouter } from "next/navigation";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import AdminHeader from "@/components/admin/AdminHeader";
import AdminSidebar from "@/components/admin/AdminSidebar";
import styles from "@/app/admin/admin.module.css";

export default function AdminLayout({ children }) {
  const { user, loading, error } = useAdminAuth();
  const pathname = usePathname();
  const router = useRouter();
  const isLoginPage = pathname === "/admin/login";

  useEffect(() => {
    if (loading) {
      return;
    }

    if (!user && !isLoginPage) {
      router.replace("/admin/login");
    } else if (user && isLoginPage) {
      router.replace("/admin");
    }
  }, [user, loading, isLoginPage, router]);

  if (loading || (!user && !isLoginPage) || (user && isLoginPage)) {
    return (
      <div
        className={styles.loading}
        role="status"
      >
        Đang kiểm tra tài khoản quản trị…
      </div>
    );
  }

  if (isLoginPage) {
    return <div className={styles.loginArea}>{children}</div>;
  }

  return (
    <div className={styles.shell}>
      <AdminSidebar />
      <div className={styles.workspace}>
        <AdminHeader />
        <section
          className={styles.content}
          aria-label="Nội dung quản trị"
        >
          {error && (
            <p
              className={styles.error}
              role="alert"
            >
              {error}
            </p>
          )}
          {children}
        </section>
      </div>
    </div>
  );
}
