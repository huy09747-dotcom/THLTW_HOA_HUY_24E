"use client";

import Link from "next/link";
import { useState } from "react";
import { imageUrl } from "@/utils/format";

function CategoryTile({ category, selected }) {
  const [failed, setFailed] = useState(false);
  const source = imageUrl(category.image);
  const active = String(category.id) === String(selected);

  return (
    <Link
      href={"/products?category=" + category.id}
      className={active ? "category-tile selected" : "category-tile"}
      aria-current={active ? "page" : undefined}
    >
      <span className="category-tile-image">
        {source && !failed ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={source}
            alt=""
            loading="lazy"
            onError={() => setFailed(true)}
          />
        ) : (
          <span
            className="category-fallback"
            aria-hidden="true"
          >
            ▦
          </span>
        )}
      </span>
      <span>{category.name}</span>
      {active && <small>Đang chọn</small>}
    </Link>
  );
}

export default function CategoryList({ categories, selected = "", compact = false, filters = {} }) {
  if (compact) {
    const items = [{ id: "", name: "Tất cả" }, ...categories];

    return (
      <nav className="category-filter-row" aria-label="Danh mục sản phẩm">
        {items.map((item) => {
          const active = String(item.id) === String(selected);
          return (
            <Link
              key={item.id}
              href={"/products?" + new URLSearchParams({ ...filters, category: String(item.id) })}
              className={active ? "category-filter selected" : "category-filter"}
              aria-current={active ? "page" : undefined}
            >
              {item.name}
            </Link>
          );
        })}
      </nav>
    );
  }

  const ids = new Set(categories.map((item) => Number(item.id)));
  let roots = categories.filter(
    (item) =>
      !ids.has(Number(item.parent_id)) || Number(item.parent_id) === Number(item.id),
  );
  // Dữ liệu cha-con lỗi vẫn phải hiển thị được danh mục.
  if (!roots.length) roots = categories;
  const displayed = new Set();

  const groups = roots.map((root) => {
    const children = categories.filter(
      (item) => Number(item.parent_id) === Number(root.id) && item.id !== root.id,
    );
    displayed.add(root.id);
    children.forEach((item) => displayed.add(item.id));
    return { root, items: children.length ? children : [root] };
  });
  const remaining = categories.filter((item) => !displayed.has(item.id));

  return (
    <nav
      className="category-directory"
      aria-label="Danh mục sản phẩm"
    >
      <Link
        href="/products"
        className="category-all"
        aria-current={!selected ? "page" : undefined}
      >
        Tất cả sản phẩm →
      </Link>
      {!categories.length && <p>Chưa có danh mục.</p>}
      <div className="category-groups">
        {groups.map(({ root, items }) => (
          <section
            key={root.id}
            className="category-group"
          >
            <h3>
              <Link href={"/products?category=" + root.id}>{root.name}</Link>
            </h3>
            <div className="category-tiles">
              {items.map((item) => (
                <CategoryTile
                  key={item.id}
                  category={item}
                  selected={selected}
                />
              ))}
            </div>
          </section>
        ))}
        {remaining.length > 0 && (
          <section className="category-group">
            <h3>Danh mục khác</h3>
            <div className="category-tiles">
              {remaining.map((item) => (
                <CategoryTile
                  key={item.id}
                  category={item}
                  selected={selected}
                />
              ))}
            </div>
          </section>
        )}
      </div>
    </nav>
  );
}
