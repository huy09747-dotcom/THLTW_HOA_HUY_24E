"use client";

import { useEffect, useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { formatPrice } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

const statuses = { 1: "Chờ xác nhận", 2: "Đã xác nhận", 3: "Đang giao", 4: "Hoàn thành", 5: "Đã hủy" };

export default function Dashboard() {
  const { token } = useAdminAuth();
  const [range, setRange] = useState("");
  const [state, setState] = useState({});
  const [retry, setRetry] = useState(0);
  useEffect(() => {
    if (!token) return;
    let active = true;
    adminApi(`/admin/dashboard?${range}`, token).then(result => {
      if (active) setState({ data: result.data, range, retry, token });
    }).catch(e => { if (active) setState({ error: e.message, range, retry, token }); });
    return () => { active = false; };
  }, [token, range, retry]);
  const pending = state.range !== range || state.retry !== retry || state.token !== token;
  const data = state.data;
  return (
    <section>
      <h1 className={styles.title}>Thống kê cửa hàng</h1>
      <form className="feature-panel feature-actions" onSubmit={e => {
        e.preventDefault();
        setRange(new URLSearchParams(new FormData(e.currentTarget)).toString());
        setRetry(n => n + 1);
      }}>
        <label>Từ ngày <input type="date" name="from" /></label>
        <label>Đến ngày <input type="date" name="to" /></label>
        <button className={styles.primaryButton}>Xem thống kê</button>
      </form>
      <p className={styles.description}>Mặc định từ đầu tháng đến hôm nay. Doanh thu là tổng tiền sau giảm giá của đơn đã hoàn thành và đã thanh toán, lọc theo ngày đặt đơn.</p>
      {pending ? <p role="status">Đang tải thống kê…</p> : state.error ? <p role="alert">{state.error}</p> : data && <>
        <div className="dashboard-metrics">
          {[["Doanh thu", formatPrice(data.revenue)], ["Đơn hàng", data.orders], ["Chờ xác nhận", data.pending], ["Đã hủy", data.cancelled]].map(([label, value]) => (
            <article className="feature-panel metric" key={label}><span>{label}</span><strong>{value}</strong></article>
          ))}
        </div>
        <div className="dashboard-columns">
          <section className="feature-panel"><h2>Doanh thu theo ngày</h2>
            {!data.daily.length ? <p>Chưa có doanh thu trong khoảng đã chọn.</p> : <table className={styles.table}><thead><tr><th>Ngày đặt</th><th>Đơn</th><th>Doanh thu</th></tr></thead><tbody>
              {data.daily.map(row => <tr key={row.day}><td>{row.day}</td><td>{row.orders}</td><td>{formatPrice(row.revenue)}</td></tr>)}
            </tbody></table>}
          </section>
          <section className="feature-panel"><h2>Trạng thái đơn hàng</h2>
            {!data.statuses.length && <p>Chưa có đơn hàng.</p>}
            {data.statuses.map(row => <div className="dashboard-status" key={row.status}><span>{statuses[row.status]}</span><strong>{row.total}</strong></div>)}
            <h2>Sản phẩm bán chạy</h2>
            {!data.top_products.length && <p>Chưa có sản phẩm trong đơn đã hoàn thành, đã thanh toán.</p>}
            {data.top_products.map(row => <div className="dashboard-status" key={row.product_id}><span>{row.name}</span><strong>{row.qty}</strong></div>)}
          </section>
        </div>
        <section className="feature-panel"><h2>Sản phẩm sắp hết hàng (tồn từ 0–5)</h2>
          {!data.low_stock.length ? <p>Không có sản phẩm sắp hết hàng.</p> : <table className={styles.table}><thead><tr><th>Sản phẩm</th><th>Tồn kho</th></tr></thead><tbody>
            {data.low_stock.map(row => <tr key={row.id}><td>{row.name}</td><td>{row.qty}</td></tr>)}
          </tbody></table>}
        </section>
      </>}
    </section>
  );
}
