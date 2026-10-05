import Link from "next/link";
import styles from "./admin.module.css";

export default function AdminPage() {
  return (
    <div>
      <p className={styles.caption}>TRANG CHÍNH</p>
      <h1 className={styles.title}>Quản trị HH Shop</h1>
      <p className={styles.description}>Chọn mục cần quản lý.</p>
      <div className={styles.cards}>
        {[
          ["categories", "Danh mục", "Quản lý danh mục và thùng rác."],
          ["products", "Sản phẩm", "Quản lý thông tin sản phẩm."],
          ["brands", "Thương hiệu", "Quản lý các thương hiệu của cửa hàng."],
        ].map(([path, title, description]) => (
          <article
            key={path}
            className={styles.card}
          >
            <h2>{title}</h2>
            <p>{description}</p>
            <Link
              href={"/admin/" + path}
              className={styles.backLink}
            >
              Mở quản lý →
            </Link>
          </article>
        ))}
      </div>
    </div>
  );
}
