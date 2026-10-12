"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import UserOrdersTable from "@/components/admin/UserOrdersTable";
import { getUserDetail } from "@/services/userServices";
import styles from "@/app/admin/admin.module.css";

export default function UserDetail({ id }) {
  const { token } = useAdminAuth();
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!token) return;
    let active = true;

    getUserDetail(id, token)
      .then((result) => {
        if (active) setUser(result.data);
      })
      .catch((exception) => {
        if (active) setError(exception.status === 404 ? "Không tìm thấy người dùng." : exception.message);
      })
      .finally(() => {
        if (active) setLoading(false);
      });

    return () => {
      active = false;
    };
  }, [id, token]);

  return (
    <>
      <div className={styles.toolbar}>
        <h1 className={styles.title}>Chi tiết người dùng</h1>
        <Link href="/admin/users" className={styles.button}>Về danh sách</Link>
      </div>
      {loading && <p role="status">Đang tải thông tin...</p>}
      {error && <p role="alert" className={styles.error}>{error}</p>}
      {user && (
        <>
          <section className={styles.card} aria-label="Thông tin tài khoản">
            <h2>{user.name}</h2>
            <dl className={styles.userProfile}>
              {[
                ["Tên đăng nhập", user.username],
                ["Email", user.email],
                ["Số điện thoại", user.phone],
                ["Địa chỉ", user.address],
                ["Ngày đăng ký", user.created_at ? new Date(user.created_at).toLocaleString("vi-VN") : null],
                ["Trạng thái", user.status === 1 ? "Hoạt động" : "Đã khóa"],
              ].map(([label, value]) => (
                <div key={label}>
                  <dt>{label}</dt>
                  <dd>{value || "—"}</dd>
                </div>
              ))}
            </dl>
          </section>
          <section className={styles.userOrders} aria-labelledby="user-orders-heading">
            <h2 id="user-orders-heading">Đơn hàng của người dùng</h2>
            <UserOrdersTable id={id} />
          </section>
        </>
      )}
    </>
  );
}
