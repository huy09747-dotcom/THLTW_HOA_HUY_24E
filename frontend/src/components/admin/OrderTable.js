"use client";

import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import {
  getOrders,
  getOrderDetail,
  updateOrderStatus,
} from "@/services/orderServices";
import { formatPrice } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

const STATUS_OPTIONS = [
  { value: 1, label: "Chờ xác nhận" },
  { value: 2, label: "Đã xác nhận" },
  { value: 3, label: "Đang giao" },
  { value: 4, label: "Hoàn thành" },
  { value: 5, label: "Đã hủy" },
];

function statusLabel(status) {
  return (
    STATUS_OPTIONS.find((item) => item.value === Number(status))?.label ??
    `Trạng thái ${status}`
  );
}

function paymentStatusLabel(status) {
  if (status === "unpaid") return "Chưa thanh toán";
  if (status === "paid") return "Đã thanh toán";
  if (status === "refunded") return "Đã hoàn tiền";
  return status || "—";
}

function paymentMethodLabel(method) {
  if (method === "cod") return "Thanh toán khi nhận hàng";
  return method || "—";
}

export default function OrderTable() {
  const { token } = useAdminAuth();

  const [page, setPage] = useState(1);
  const [orders, setOrders] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [busyId, setBusyId] = useState(null);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [detailLoading, setDetailLoading] = useState(false);
  const [detailError, setDetailError] = useState("");

  async function loadOrders() {
    if (!token) return;

    setLoading(true);
    setError("");

    try {
      const result = await getOrders(page, token);

      // Hỗ trợ cả response dạng mảng và Laravel pagination.
      const rows = Array.isArray(result)
        ? result
        : Array.isArray(result?.data)
          ? result.data
          : [];

      setOrders(rows);
      setCurrentPage(result?.meta?.current_page ?? result?.current_page ?? page);
      setLastPage(result?.meta?.last_page ?? result?.last_page ?? 1);
    } catch (exception) {
      setError(
        exception?.response?.data?.message ||
          exception.message ||
          "Không thể tải danh sách đơn hàng."
      );
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadOrders();
    // loadOrders được gọi khi trang hoặc token thay đổi.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [page, token]);

  async function handleViewDetail(id) {
    setSelectedOrder(null);
    setDetailError("");
    setDetailLoading(true);

    try {
      const result = await getOrderDetail(id, token);
      setSelectedOrder(result?.data ?? result);
    } catch (exception) {
      setDetailError(
        exception?.response?.data?.message ||
          exception.message ||
          "Không thể tải chi tiết đơn hàng."
      );
    } finally {
      setDetailLoading(false);
    }
  }

  async function handleUpdateStatus(order, nextStatus) {
    if (Number(nextStatus) === Number(order.status)) return;

    const confirmed = window.confirm(
      `Bạn có chắc muốn đổi trạng thái đơn ${order.code || order.id} thành "${statusLabel(nextStatus)}"?`
    );

    if (!confirmed) return;

    setBusyId(order.id);
    setError("");

    try {
      await updateOrderStatus(order.id, Number(nextStatus), token);

      await loadOrders();

      if (selectedOrder?.id === order.id) {
        const result = await getOrderDetail(order.id, token);
        setSelectedOrder(result?.data ?? result);
      }
    } catch (exception) {
      setError(
        exception?.response?.data?.message ||
          exception.message ||
          "Không thể cập nhật trạng thái đơn hàng."
      );
    } finally {
      setBusyId(null);
    }
  }

  if (!token) {
    return <p>Vui lòng đăng nhập tài khoản quản trị.</p>;
  }

  return (
    <div>
      {error && (
        <p role="alert" style={{ color: "red", marginBottom: 16 }}>
          {error}
        </p>
      )}

      {loading ? (
        <p role="status">Đang tải danh sách đơn hàng...</p>
      ) : orders.length === 0 ? (
        <p>Chưa có đơn hàng nào.</p>
      ) : (
        <>
          <div style={{ overflowX: "auto" }}>
            <table className={styles.table}>
              <thead>
                <tr>
                  <th>Mã đơn</th>
                  <th>Khách hàng</th>
                  <th>Số điện thoại</th>
                  <th>Tổng tiền</th>
                  <th>Thanh toán</th>
                  <th>Trạng thái</th>
                  <th>Ngày đặt</th>
                  <th>Thao tác</th>
                </tr>
              </thead>

              <tbody>
                {orders.map((order) => (
                  <tr key={order.id}>
                    <td>{order.code || order.id}</td>
                    <td>{order.name || order.nguoidung?.name || "—"}</td>
                    <td>{order.phone || "—"}</td>
                    <td>{formatPrice(order.total ?? 0)}</td>

                    <td>
                      <div>{paymentMethodLabel(order.payment_method)}</div>
                      <small>{paymentStatusLabel(order.payment_status)}</small>
                    </td>

                    <td>
                      <select
                        aria-label={`Trạng thái đơn ${order.code || order.id}`}
                        value={Number(order.status)}
                        disabled={busyId === order.id}
                        onChange={(event) =>
                          handleUpdateStatus(order, event.target.value)
                        }
                      >
                        {STATUS_OPTIONS.map((status) => (
                          <option key={status.value} value={status.value}>
                            {status.label}
                          </option>
                        ))}
                      </select>

                      {busyId === order.id && (
                        <small> Đang cập nhật...</small>
                      )}
                    </td>

                    <td>
                      {order.created_at
                        ? new Date(order.created_at).toLocaleString("vi-VN")
                        : "—"}
                    </td>

                    <td>
                      <button
                        type="button"
                        className={styles.button}
                        onClick={() => handleViewDetail(order.id)}
                      >
                        Xem chi tiết
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <nav
            className={styles.pagination}
            aria-label="Phân trang đơn hàng"
          >
            <button
              type="button"
              className={styles.button}
              disabled={page <= 1}
              onClick={() => setPage((value) => value - 1)}
            >
              Trang trước
            </button>

            <span>
              Trang {currentPage} / {lastPage}
            </span>

            <button
              type="button"
              className={styles.button}
              disabled={page >= lastPage}
              onClick={() => setPage((value) => value + 1)}
            >
              Trang sau
            </button>
          </nav>
        </>
      )}

      {(detailLoading || detailError || selectedOrder) && (
        <div
          role="dialog"
          aria-modal="true"
          aria-label="Chi tiết đơn hàng"
          style={{
            position: "fixed",
            inset: 0,
            zIndex: 1000,
            background: "rgba(0,0,0,0.45)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            padding: 16,
          }}
        >
          <section
            style={{
              background: "white",
              color: "#222",
              width: "100%",
              maxWidth: 800,
              maxHeight: "90vh",
              overflowY: "auto",
              borderRadius: 8,
              padding: 24,
            }}
          >
            <div
              style={{
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                gap: 12,
              }}
            >
              <h2>Chi tiết đơn hàng</h2>

              <button
                type="button"
                className={styles.button}
                onClick={() => {
                  setSelectedOrder(null);
                  setDetailError("");
                }}
              >
                Đóng
              </button>
            </div>

            {detailLoading && <p>Đang tải chi tiết đơn hàng...</p>}

            {detailError && (
              <p role="alert" style={{ color: "red" }}>
                {detailError}
              </p>
            )}

            {selectedOrder && (
              <>
                <p>
                  <strong>Mã đơn:</strong>{" "}
                  {selectedOrder.code || selectedOrder.id}
                </p>
                <p>
                  <strong>Khách hàng:</strong>{" "}
                  {selectedOrder.name || "—"}
                </p>
                <p>
                  <strong>Số điện thoại:</strong>{" "}
                  {selectedOrder.phone || "—"}
                </p>
                <p>
                  <strong>Email:</strong> {selectedOrder.email || "—"}
                </p>
                <p>
                  <strong>Địa chỉ:</strong> {selectedOrder.address || "—"}
                </p>
                <p>
                  <strong>Ghi chú:</strong> {selectedOrder.note || "—"}
                </p>
                <p>
                  <strong>Trạng thái:</strong>{" "}
                  {statusLabel(selectedOrder.status)}
                </p>

                <h3 style={{ marginTop: 20, marginBottom: 12 }}>
                  Sản phẩm đã đặt
                </h3>

                {Array.isArray(selectedOrder.items) &&
                selectedOrder.items.length > 0 ? (
                  <div style={{ overflowX: "auto" }}>
                    <table className={styles.table}>
                      <thead>
                        <tr>
                          <th>Sản phẩm</th>
                          <th>Đơn giá</th>
                          <th>Số lượng</th>
                          <th>Thành tiền</th>
                        </tr>
                      </thead>
                      <tbody>
                        {selectedOrder.items.map((item) => (
                          <tr key={item.id}>
                            <td>
                              {item.item_name ||
                                item.sanpham?.name ||
                                `Sản phẩm #${item.product_id}`}
                            </td>
                            <td>{formatPrice(item.price ?? 0)}</td>
                            <td>{item.qty}</td>
                            <td>{formatPrice(item.amount ?? 0)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                ) : (
                  <p>Đơn hàng chưa có chi tiết sản phẩm.</p>
                )}

                <h3 style={{ marginTop: 20 }}>
                  Tổng tiền: {formatPrice(selectedOrder.total ?? 0)}
                </h3>
              </>
            )}
          </section>
        </div>
      )}
    </div>
  );
}