import OrderTable from "@/components/admin/OrderTable";
import styles from "../admin.module.css";

export const metadata = { title: "Đơn hàng | HH Shop" };

export default function OrdersPage() {
  return (
    <div>
      <h1 className={styles.title}>Danh sách đơn hàng</h1>
      <OrderTable />
    </div>
  );
}
