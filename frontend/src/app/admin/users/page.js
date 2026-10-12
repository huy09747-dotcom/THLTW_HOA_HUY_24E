import UserTable from "@/components/admin/UserTable";
import styles from "../admin.module.css";

export const metadata = { title: "Người dùng | HH Shop" };

export default function UsersPage() {
  return (
    <div>
      <h1 className={styles.title}>Quản lý người dùng</h1>
      <UserTable />
    </div>
  );
}
