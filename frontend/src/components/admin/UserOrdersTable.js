"use client";

import { useEffect, useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { getUserOrders } from "@/services/userServices";
import { formatPrice } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

const statuses = {
  1: "Chờ xác nhận",
  2: "Đã xác nhận",
  3: "Đang giao",
  4: "Hoàn thành",
  5: "Đã hủy",
};

const payments = {
  unpaid: "Chưa thanh toán",
  paid: "Đã thanh toán",
  refunded: "Đã hoàn tiền",
};

export default function UserOrdersTable({ id }) {
  const { token } = useAdminAuth();
  const [page, setPage] = useState(1);
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!token) return;
    let active = true;

    getUserOrders(id, page, token)
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
  }, [id, page, token]);

  function changePage(nextPage) {
    setLoading(true);
    setError("");
    setPage(nextPage);
  }

  return (
    <>
      {error && <p role="alert" className={styles.error}>{error}</p>}
      {loading ? (
        <p role="status">Đang tải đơn hàng...</p>
      ) : (
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>STT</th>
                <th>Mã đơn</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
              </tr>
            </thead>
            <tbody>
              {result?.data.map((order, index) => (
                <tr key={order.id}>
                  <td>{(result.from ?? 1) + index}</td>
                  <td>{order.code || `#${order.id}`}</td>
                  <td>{formatPrice(order.total)}</td>
                  <td>{payments[order.payment_status] || order.payment_status || "—"}</td>
                  <td>{statuses[order.status] || "—"}</td>
                  <td>{order.created_at ? new Date(order.created_at).toLocaleString("vi-VN") : "—"}</td>
                </tr>
              ))}
              {result?.data.length === 0 && (
                <tr><td colSpan={6}>Người dùng chưa có đơn hàng.</td></tr>
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
            disabled={loading || page <= 1}
            onClick={() => changePage(page - 1)}
          >
            Trang trước
          </button>
          <span>Trang {page} / {result.last_page}</span>
          <button
            type="button"
            className={styles.button}
            disabled={loading || page >= result.last_page}
            onClick={() => changePage(page + 1)}
          >
            Trang sau
          </button>
        </div>
      )}
    </>
  );
}
