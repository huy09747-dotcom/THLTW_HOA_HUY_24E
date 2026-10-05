"use client";

import styles from "@/app/admin/admin.module.css";

export default function EntityForm({
  fields,
  initial = {},
  onSave,
  onCancel,
  busy,
  options = {},
}) {
  async function submit(event) {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    const body = {};

    for (const field of fields) {
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

    await onSave(body);
  }

  return (
    <form
      onSubmit={submit}
      className={styles.editor}
    >
      <fieldset disabled={busy}>
        <legend>{initial.id ? "Chỉnh sửa thông tin" : "Thêm mới"}</legend>
        <p>
          Các trường có dấu * là bắt buộc. Hình ảnh nhập đường dẫn, không tải file lên.
        </p>
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
                {field.type === "textarea" ? (
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
                    defaultValue={value}
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
