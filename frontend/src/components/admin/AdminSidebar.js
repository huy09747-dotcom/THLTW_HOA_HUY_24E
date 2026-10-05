"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import styles from "@/app/admin/admin.module.css";

const links = [
  ["/admin", "Trang chính"],
  ["/admin/categories", "Danh mục"],
  ["/admin/products", "Sản phẩm"],
  ["/admin/brands", "Thương hiệu"],
];

export default function AdminSidebar() {
  const pathname = usePathname();

  return (
    <aside className={styles.sidebar}>
      <Link
        href="/admin"
        className={styles.brand}
      >
        HH Shop
      </Link>
      <p className={styles.caption}>KHU VỰC QUẢN TRỊ</p>
      <nav
        aria-label="Menu quản trị"
        className={styles.navigation}
      >
        {links.map(([href, label]) => {
          const selected =
            href === "/admin" ? pathname === href : pathname.startsWith(href);
          return (
            <Link
              key={href}
              href={href}
              className={selected ? styles.active : styles.menuLink}
              aria-current={selected ? "page" : undefined}
            >
              {label}
            </Link>
          );
        })}
      </nav>
      <Link
        href="/"
        className={styles.storeLink}
      >
        ← Về cửa hàng
      </Link>
    </aside>
  );
}
