"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { getUsers, updateUserStatus } from "@/services/userServices";
import styles from "@/app/admin/admin.module.css";

export default function UserTable() {
  const { token } = useAdminAuth();
  const [filters, setFilters] = useState("");
  const [revision, setRevision] = useState(0);
  const [page, setPage] = useState(1);
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [busyId, setBusyId] = useState(null);

  useEffect(() => {
    if (!token) return;
    let active = true;

    getUsers(page, token, filters)
      .then((data) => {
        if (active) setResult(data);
      })
      .catch((exception) => {
        if (active) setError(exception.message);
      })
      .finally(() => {
        if (active) setLoading(false);
      });

    return () => {
      active = false;
    };
  }, [page, token, filters, revision]);

  function changePage(nextPage) {
    setLoading(true);
    setError("");
    setPage(nextPage);
  }

  async function changeStatus(user) {
    const status = user.status === 1 ? 0 : 1;
    const action = status === 0 ? "khóa" : "mở khóa";
    if (!window.confirm(`Bạn muốn ${action} tài khoản ${user.name}?`)) return;

    setBusyId(user.id);
    setError("");
    try {
      const response = await updateUserStatus(user.id, status, token);
      setResult((current) => ({
        ...current,
        data: current.data.map((item) =>
          item.id === user.id ? response.data : item
        ),
      }));
    } catch (exception) {
      setError(exception.message);
    } finally {
      setBusyId(null);
    }
  }

  return (
    <>
      <form className="feature-panel feature-actions" onSubmit={e => {e.preventDefault();setLoading(true);setPage(1);setFilters(new URLSearchParams(new FormData(e.currentTarget)).toString());setRevision(n=>n+1);}}>
        <input name="search" placeholder="Tên / email / số điện thoại" maxLength={255} aria-label="Tìm thành viên" />
        <select name="status" aria-label="Trạng thái"><option value="">Tất cả</option><option value="1">Hoạt động</option><option value="0">Đã khóa</option></select><button>Lọc</button>
      </form>
      {error && <p className={styles.error} role="alert">{error}</p>}
      {loading ? (
        <p role="status">Đang tải người dùng...</p>
      ) : (
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              {result?.data.map((user, index) => (
                <tr key={user.id}>
                  <td>{(result.meta.from ?? 1) + index}</td>
                  <td>{user.name}</td>
                  <td>{user.email}</td>
                  <td>{user.phone || "—"}</td>
                  <td>
                    <span className={user.status === 1 ? styles.userActive : styles.userBlocked}>
                      {user.status === 1 ? "Hoạt động" : "Đã khóa"}
                    </span>
                  </td>
                  <td>
                    <div className={styles.actions}>
                      <button className={styles.dangerButton} disabled={busyId !== null} onClick={async () => {
                        if (!window.confirm(`Xóa tài khoản ${user.name}?`)) return;
                        setBusyId(user.id); setError("");
                        try { await adminApi(`/admin/users/${user.id}`,token,{method:"DELETE"});setPage(1);setRevision(n=>n+1); }
                        catch(e){setError(e.message);} finally{setBusyId(null);}
                      }}>Xóa</button>
                      <Link href={`/admin/users/${user.id}`} className={styles.button}>
                        Chi tiết
                      </Link>
                      <button
                        type="button"
                        className={user.status === 1 ? styles.dangerButton : styles.button}
                        disabled={busyId !== null}
                        onClick={() => changeStatus(user)}
                      >
                        {busyId === user.id ? "Đang lưu..." : user.status === 1 ? "Khóa" : "Mở khóa"}
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {result?.data.length === 0 && (
                <tr><td colSpan={6}>Chưa có người dùng.</td></tr>
              )}
            </tbody>
          </table>
        </div>
      )}
      {result && (
        <div className={styles.pagination}>
          <button
            type="button"
            className={styles.button}
            disabled={loading || busyId !== null || page <= 1}
            onClick={() => changePage(page - 1)}
          >
            Trang trước
          </button>
          <span>Trang {page} / {result.meta.last_page}</span>
          <button
            type="button"
            className={styles.button}
            disabled={loading || busyId !== null || page >= result.meta.last_page}
            onClick={() => changePage(page + 1)}
          >
            Trang sau
          </button>
        </div>
      )}
    </>
  );
}
