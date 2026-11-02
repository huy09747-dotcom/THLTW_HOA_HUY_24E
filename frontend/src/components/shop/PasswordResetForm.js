"use client";

import Link from "next/link";
import { useState } from "react";
import { api } from "@/services/api";

export default function PasswordResetForm({ token = "", email = "" }) {
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  const [done, setDone] = useState(false);
  const resetting = Boolean(token);

  async function submit(event) {
    event.preventDefault();
    const body = Object.fromEntries(new FormData(event.currentTarget));
    if (resetting && body.password !== body.password_confirmation) {
      setError("Xác nhận mật khẩu không khớp.");
      return;
    }
    setBusy(true);
    setError("");
    setNotice("");
    try {
      const result = await api(resetting ? "/reset-password" : "/forgot-password", {
        method: "POST",
        body: { ...body, ...(resetting ? { token } : {}) },
      });
      setNotice(result.message);
      if (resetting) setDone(true);
    } catch (exception) {
      setError(exception.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="auth-page">
      <p className="eyebrow">HH SHOP</p>
      <h1>{resetting ? "Đặt lại mật khẩu" : "Quên mật khẩu"}</h1>
      {!resetting && <p>Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>}
      {error && <p className="error-message" role="alert">{error}</p>}
      {notice && <p role="status">{notice}</p>}
      {!done && (
        <form className="auth-form" onSubmit={submit}>
          <div>
            <label htmlFor="reset-email">Email đăng ký</label>
            <input id="reset-email" name="email" type="email" autoComplete="email"
              defaultValue={email} maxLength={255} required disabled={busy} />
          </div>
          {resetting && (
            <>
              <div>
                <label htmlFor="new-password">Mật khẩu mới</label>
                <input id="new-password" name="password" type="password"
                  autoComplete="new-password" minLength={8} maxLength={255} required disabled={busy} />
              </div>
              <div>
                <label htmlFor="confirm-password">Xác nhận mật khẩu mới</label>
                <input id="confirm-password" name="password_confirmation" type="password"
                  autoComplete="new-password" minLength={8} maxLength={255} required disabled={busy} />
              </div>
            </>
          )}
          <button className="primary-button" disabled={busy}>
            {busy ? "Đang xử lý…" : resetting ? "Lưu mật khẩu mới" : "Gửi liên kết"}
          </button>
        </form>
      )}
      <div className="auth-links">
        <Link href="/login">← Quay lại đăng nhập</Link>
        {resetting && <Link href="/forgot-password">Yêu cầu liên kết mới</Link>}
      </div>
    </section>
  );
}
