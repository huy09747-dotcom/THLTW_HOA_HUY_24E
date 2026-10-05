"use client";

import { useEffect, useRef, useState } from "react";
import { usePathname } from "next/navigation";
import { getCategories } from "@/services/categoryServices";
import CategoryList from "./CategoryList";

export default function CategoryMenu() {
  const [open, setOpen] = useState(false);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const menu = useRef(null);
  const button = useRef(null);
  const pathname = usePathname();

  async function toggle() {
    setOpen(!open);
    if (open || categories.length || loading) return;
    setLoading(true);
    setError("");
    try {
      setCategories(await getCategories());
    } catch (exception) {
      setError(exception.message);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    function close(event) {
      if (!menu.current?.contains(event.target)) setOpen(false);
    }
    function escape(event) {
      if (event.key === "Escape") {
        setOpen(false);
        button.current?.focus();
      }
    }
    document.addEventListener("pointerdown", close);
    document.addEventListener("keydown", escape);
    return () => {
      document.removeEventListener("pointerdown", close);
      document.removeEventListener("keydown", escape);
    };
  }, []);

  return (
    <div
      className="category-menu"
      ref={menu}
    >
      <button
        ref={button}
        type="button"
        className="category-menu-trigger"
        onClick={toggle}
        aria-expanded={open}
        aria-controls="category-menu-panel"
      >
        Danh mục <span aria-hidden="true">{open ? "⌃" : "⌄"}</span>
      </button>
      {open && (
        <div
          id="category-menu-panel"
          className="category-menu-panel"
          key={pathname}
        >
          <div className="category-menu-heading">
            <strong>Khám phá theo danh mục</strong>
            <button
              type="button"
              onClick={() => setOpen(false)}
              aria-label="Đóng danh mục"
            >
              ×
            </button>
          </div>
          {loading ? (
            <p role="status">Đang tải danh mục…</p>
          ) : error ? (
            <p role="alert">{error}</p>
          ) : (
            <div
              onClick={(event) => {
                if (event.target.closest("a")) setOpen(false);
              }}
            >
              <CategoryList categories={categories} />
            </div>
          )}
        </div>
      )}
    </div>
  );
}
