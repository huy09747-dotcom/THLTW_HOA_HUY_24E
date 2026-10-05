"use client";

import { useState } from "react";
import { useAdminAuth } from "./AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import AdminImage from "./AdminImage";
import { toSlug } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

export default function EntityForm({
  fields,
  initial = {},
  onSave,
  onCancel,
  busy,
  options = {},
}) {
  const { token } = useAdminAuth();
  const [slug, setSlug] = useState(initial.slug ?? toSlug(initial.name));
  const [preview, setPreview] = useState("");
  const [uploading, setUploading] = useState(false);
  const [uploadError, setUploadError] = useState("");
  const [uploaded, setUploaded] = useState(null);

  async function submit(event) {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    const body = {};
    setUploadError("");

    for (const field of fields) {
      if (field.name === "image") continue;
      const value = data.get(field.name);
      body[field.name] =
        field.type === "checkbox"
          ? data.has(field.name)
          : value === "" && field.nullable
            ? null
            : field.type === "number" || field.type === "select"
              ? Number(value)
              : value;
    }

    setUploading(true);
    try {
      body.image = initial.image || null;
      const file = data.get("image");
      if (file?.size) {
        if (uploaded?.key === file.name + file.size + file.lastModified) {
          body.image = uploaded.path;
        } else {
          const payload = new FormData();
          payload.append("image", file);
          const result = await adminApi("/images", token, { method: "POST", body: payload });
          body.image = result.image;
          setUploaded({ key: file.name + file.size + file.lastModified, path: result.image });
        }
      }
      await onSave(body);
    } catch (exception) {
      setUploadError(exception.message);
    } finally {
      setUploading(false);
    }
  }

  return (
    <form
      onSubmit={submit}
      className={styles.editor}
    >
      <fieldset disabled={busy || uploading}>
        <legend>{initial.id ? "Chỉnh sửa thông tin" : "Thêm mới"}</legend>
        {uploadError && <p role="alert" className={styles.error}>{uploadError}</p>}
        <div className={styles.fieldGrid}>
          {fields.map((field) => {
            const id = "field-" + field.name;
            const value = initial[field.name] ?? field.default ?? "";
            let choices = options[field.name] || field.options || [];

            // Giữ liên kết hiện tại nếu bản ghi không có trong danh sách đang hoạt động.
            if (
              field.type === "select" &&
              value !== "" &&
              !choices.some((item) => String(item.id) === String(value))
            ) {
              choices = [...choices, { id: value, name: "Giá trị hiện tại: " + value }];
            }

            return (
              <div
                key={field.name}
                className={field.type === "textarea" ? styles.fullField : undefined}
              >
                <label htmlFor={id}>
                  {field.label}
                  {field.required ? " *" : ""}
                </label>
                {field.name === "image" ? (
                  <>
                    <input
                      id={id}
                      name="image"
                      type="file"
                      accept="image/jpeg,image/png,image/webp"
                      required={field.required && !initial.image}
                      onChange={(event) => {
                        const input = event.currentTarget;
                        const file = input.files?.[0];
                        setPreview("");
                        setUploaded(null);
                        setUploadError("");
                        if (!file) return;
                        if (file.size > 5 * 1024 * 1024) {
                          input.value = "";
                          setUploadError("Hinh anh toi da 5 MB.");
                          return;
                        }
                        const reader = new FileReader();
                        reader.onload = () => {
                          if (input.files?.[0] === file) setPreview(reader.result);
                        };
                        reader.readAsDataURL(file);
                      }}
                    />
                    {preview ? (
                      // eslint-disable-next-line @next/next/no-img-element
                      <img src={preview} alt="Ảnh đã chọn" className={styles.thumbnail} />
                    ) : initial.image ? (
                      <AdminImage image={initial.image} name="Ảnh hiện tại" />
                    ) : null}
                  </>
                ) : field.type === "textarea" ? (
                  <textarea
                    id={id}
                    name={field.name}
                    defaultValue={value}
                    required={field.required}
                    rows={4}
                  />
                ) : field.type === "select" ? (
                  <select
                    id={id}
                    name={field.name}
                    defaultValue={value}
                    required={field.required}
                  >
                    <option value="">Chọn {field.label.toLowerCase()}</option>
                    {choices.map((item) => (
                      <option
                        key={item.id}
                        value={item.id}
                      >
                        {item.name}
                      </option>
                    ))}
                  </select>
                ) : field.type === "checkbox" ? (
                  <input
                    id={id}
                    name={field.name}
                    type="checkbox"
                    defaultChecked={Number(value) === 1}
                  />
                ) : (
                  <input
                    id={id}
                    name={field.name}
                    type={field.type || "text"}
                    {...(field.name === "slug"
                      ? { value: slug, onChange: (event) => setSlug(event.target.value) }
                      : {
                          defaultValue: value,
                          onChange:
                            field.name === "name"
                              ? (event) => setSlug(toSlug(event.target.value))
                              : undefined,
                        })}
                    required={field.required}
                    maxLength={field.maxLength}
                    min={field.min}
                    max={field.max}
                    step={field.step || (field.type === "number" ? "1" : undefined)}
                  />
                )}
              </div>
            );
          })}
        </div>
        <div className={styles.actions}>
          <button
            type="submit"
            className={styles.primaryButton}
          >
            {busy ? "Đang lưu…" : "Lưu"}
          </button>
          <button
            type="button"
            onClick={onCancel}
            className={styles.button}
          >
            Hủy
          </button>
        </div>
      </fieldset>
    </form>
  );
}
