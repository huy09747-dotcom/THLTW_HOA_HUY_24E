"use client";

import { useState } from "react";
import styles from "@/app/admin/admin.module.css";

const types = {
  custom: "Liên kết tùy chỉnh",
  category: "Danh mục",
  brand: "Thương hiệu",
  topic: "Chủ đề bài viết",
  page: "Trang đơn",
};

export default function MenuForm({ initial, options, onSave, onCancel, busy }) {
  const [type, setType] = useState(initial.type || "custom");
  const [target, setTarget] = useState(initial.table_id ?? "");
  const choices = options[type] || [];

  function submit(event) {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    onSave({
      name: data.get("name"),
      link: data.get("link"),
      type,
      table_id: type === "custom" ? null : Number(target),
      status: Number(data.get("status")),
    });
  }

  return (
    <form onSubmit={submit} className={styles.editor}>
      <fieldset disabled={busy}>
        <legend>{initial.id ? "Chỉnh sửa menu" : "Thêm menu"}</legend>
        <div className={styles.fieldGrid}>
          <div>
            <label htmlFor="menu-name">Tên menu *</label>
            <input id="menu-name" name="name" defaultValue={initial.name || ""} maxLength={255} required />
          </div>
          <div>
            <label htmlFor="menu-link">Đường dẫn *</label>
            <input id="menu-link" name="link" defaultValue={initial.link || ""} placeholder="/products" maxLength={255} required />
          </div>
          <div>
            <label htmlFor="menu-type">Loại menu *</label>
            <select
              id="menu-type"
              value={type}
              onChange={(event) => {
                setType(event.target.value);
                setTarget("");
              }}
            >
              {Object.entries(types).map(([value, label]) => (
                <option key={value} value={value}>{label}</option>
              ))}
            </select>
          </div>
          {type !== "custom" && (
            <div>
              <label htmlFor="menu-target">Đối tượng liên kết *</label>
              <select id="menu-target" value={target} onChange={(event) => setTarget(event.target.value)} required>
                <option value="" disabled hidden>Chọn đối tượng liên kết</option>
                {target !== "" && !choices.some((item) => String(item.id) === String(target)) && (
                  <option value={target} disabled>Liên kết không còn hợp lệ — hãy chọn lại</option>
                )}
                {choices.map((item) => (
                  <option key={item.id} value={item.id}>{item.name}</option>
                ))}
              </select>
            </div>
          )}
          <div>
            <label htmlFor="menu-status">Trạng thái *</label>
            <select id="menu-status" name="status" defaultValue={initial.status ?? 1}>
              <option value="1">Hoạt động</option>
              <option value="0">Ẩn</option>
              <option value="2">Chưa xuất bản</option>
            </select>
          </div>
        </div>
        <div className={styles.actions}>
          <button type="submit" className={styles.primaryButton}>{busy ? "Đang lưu..." : "Lưu"}</button>
          <button type="button" className={styles.button} onClick={onCancel}>Hủy</button>
        </div>
      </fieldset>
    </form>
  );
}
