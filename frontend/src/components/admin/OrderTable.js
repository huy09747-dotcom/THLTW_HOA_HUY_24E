"use client";

import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import { getOrders } from "@/services/orderServices";
import { formatPrice } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

export default function OrderTable() {
  const { token } = useAdminAuth();
  const [page, setPage] = useState(1);
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!token) return;
    let active = true;

    async function load() {
      setLoading(true);
      setError("");
      try {
        const data = await getOrders(page, token);
        if (active) setResult(data);
      } catch (exception) {
        if (active) setError(exception.message);
      } finally {
        if (active) setLoading(false);
      }
    }

    load();
    return () => {
      active = false;
    };
  }, [page, token]);

  if (loading) return <p role="status">Đang tải đơn hàng…</p>;
  if (error) return <p role="alert" className={styles.error}>{error}</p>;
  if (!result?.data.length) return <p>Chưa có đơn hàng.</p>;

  return (
    <>
      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Mã đơn</th>
              <th>Người nhận</th>
              <th>Điện thoại</th>
              <th>Địa chỉ</th>
              <th>Tổng tiền</th>
              <th>Thanh toán</th>
              <th>Trạng thái</th>
              <th>Ngày đặt</th>
            </tr>
          </thead>
          <tbody>
            {result.data.map((order) => (
              <tr key={order.id}>
                <td>{order.id}</td>
                <td>{order.code || "—"}</td>
                <td>{order.name}</td>
                <td>{order.phone}</td>
                <td>{order.address}</td>
                <td>{formatPrice(order.total)}</td>
                <td>
                  <div>
                    {order.payment_method === "cod"
                      ? "Thanh toán khi nhận hàng"
                      : order.payment_method || "—"}
                  </div>
                  <span>
                    {order.payment_status === "unpaid"
                      ? "Chưa thanh toán"
                      : order.payment_status === "paid"
                        ? "Đã thanh toán"
                        : order.payment_status || "—"}
                  </span>
                </td>
                <td>{order.status}</td>
                <td>
                  {order.created_at
                    ? new Date(order.created_at).toLocaleString("vi-VN")
                    : "—"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <nav className={styles.pagination} aria-label="Phân trang đơn hàng">
        <button
          className={styles.button}
          disabled={page <= 1}
          onClick={() => setPage(page - 1)}
        >
          Trang trước
        </button>
        <span>Trang {result.meta.current_page} / {result.meta.last_page}</span>
        <button
          className={styles.button}
          disabled={page >= result.meta.last_page}
          onClick={() => setPage(page + 1)}
        >
          Trang sau
        </button>
      </nav>
    </>
  );
}
