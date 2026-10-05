import Link from "next/link";

export default function CategoryList({ categories, selected = "" }) {
  return (
    <nav
      className="category-list"
      aria-label="Danh mục sản phẩm"
    >
      <Link
        href="/products"
        className={!selected ? "category-chip selected" : "category-chip"}
        aria-current={!selected ? "page" : undefined}
      >
        Tất cả
      </Link>
      {categories.map((category) => (
        <Link
          key={category.id}
          href={`/products?category=${category.id}`}
          className={
            String(category.id) === String(selected)
              ? "category-chip selected"
              : "category-chip"
          }
          aria-current={String(category.id) === String(selected) ? "page" : undefined}
        >
          {category.name}
        </Link>
      ))}
    </nav>
  );
}
