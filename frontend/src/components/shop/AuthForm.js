"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useCart } from "./CartProvider";

export default function AuthForm({ registerMode = false }) {
  const { user, login, register, logout, loading, busy, error } = useCart();
  const [validation, setValidation] = useState("");
  const router = useRouter();

  async function submit(event) {
    event.preventDefault();
    setValidation("");
    const form = event.currentTarget;
    const body = Object.fromEntries(new FormData(form));

    if (registerMode && body.password !== body.password_confirmation) {
      setValidation("Mat khau xac nhan khong khop.");
      return;
    }

    const success = registerMode
      ? await register(body)
      : await login(body.email, body.password);

    if (success) {
      form.reset();
      router.replace("/");
    }
  }

  if (loading) {
    return (
      <p
        className="container catalog-page"
        role="status"
      >
        Đang kiểm tra tài khoản…
      </p>
    );
  }

  if (user) {
    return (
      <section className="auth-page">
        <h1>Tài khoản của bạn</h1>
        <p>Xin chào, {user.name}</p>
        <p>{user.email}</p>
        {error && (
          <p
            role="alert"
            className="error-message"
          >
            {error}
          </p>
        )}
        <div className="auth-links">
          <Link href="/profile">Hồ sơ cá nhân</Link>
          <Link href="/orders">Đơn hàng của tôi</Link>
          <Link href="/products">Tiếp tục mua sắm</Link>
          <button
            type="button"
            className="primary-button"
            onClick={logout}
            disabled={busy}
          >
            Đăng xuất
          </button>
        </div>
      </section>
    );
  }

  const fields = registerMode
    ? [
        ["name", "Họ và tên", "text", "name"],
        ["email", "Email", "email", "email"],
        ["phone", "Số điện thoại", "tel", "tel"],
        ["address", "Địa chỉ (không bắt buộc)", "text", "street-address"],
        ["password", "Mật khẩu (ít nhất 8 ký tự)", "password", "new-password"],
        ["password_confirmation", "Xác nhận mật khẩu", "password", "new-password"],
      ]
    : [
        ["email", "Email", "email", "username"],
        ["password", "Mật khẩu", "password", "current-password"],
      ];

  return (
    <section className={`auth-page${registerMode ? " register-page" : ""}`}>
      <p className="eyebrow">HH SHOP</p>
      <h1>{registerMode ? "Đăng ký thành viên" : "Đăng nhập"}</h1>
      <p>
        {registerMode
          ? "Tạo tài khoản để mua sắm tại HH Shop."
          : "Đăng nhập để tiếp tục mua sắm."}
      </p>
      {(validation || error) && (
        <p
          role="alert"
          className="error-message"
        >
          {validation || error}
        </p>
      )}
      <form
        onSubmit={submit}
        className="auth-form"
      >
        {fields.map(([name, label, type, autoComplete]) => (
          <div key={name}>
            <label htmlFor={name}>{label}</label>
            <input
              id={name}
              name={name}
              type={type}
              autoComplete={autoComplete}
              required={name !== "address"}
              maxLength={255}
              minLength={registerMode && type === "password" ? 8 : undefined}
              disabled={busy}
            />
          </div>
        ))}
        <button
          className="primary-button"
          disabled={busy}
        >
          {busy ? "Đang xử lý…" : registerMode ? "Đăng ký" : "Đăng nhập"}
        </button>
      </form>
      {!registerMode && <p><Link href="/forgot-password">Quên mật khẩu?</Link></p>}
      <p>
        {registerMode ? "Đã có tài khoản? " : "Chưa có tài khoản? "}
        <Link href={registerMode ? "/login" : "/register"}>
          {registerMode ? "Đăng nhập" : "Đăng ký ngay"}
        </Link>
      </p>
    </section>
  );
}
