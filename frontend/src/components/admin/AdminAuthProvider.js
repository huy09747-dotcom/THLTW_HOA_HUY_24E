"use client";

import { createContext, useContext, useEffect, useState } from "react";

const AdminAuthContext = createContext(null);
const SESSION_KEY = "hh-shop-admin-demo-session";
const DEMO_TOKEN = "hh-shop-admin-preview";
const DEMO_ACCOUNT = { name: "Admin HH Shop", email: "admin@gmail.com" };

export default function AdminAuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState("");
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    async function restoreSession() {
      if (sessionStorage.getItem(SESSION_KEY) === "active") {
        setUser(DEMO_ACCOUNT);
        setToken(DEMO_TOKEN);
      }
      setLoading(false);
    }

    restoreSession();
  }, []);

  async function login(email, password) {
    setBusy(true);
    setError("");

    try {
      // Tài khoản mẫu chỉ dùng trong frontend, không xác thực qua Laravel.
      if (email.trim().toLowerCase() !== DEMO_ACCOUNT.email || password !== "admin123") {
        setError("Tai khoan hoac mat khau khong dung.");
        return false;
      }

      sessionStorage.setItem(SESSION_KEY, "active");
      setUser(DEMO_ACCOUNT);
      setToken(DEMO_TOKEN);
      return true;
    } finally {
      setBusy(false);
    }
  }

  function logout() {
    sessionStorage.removeItem(SESSION_KEY);
    setUser(null);
    setToken("");
    setError("");
  }

  return (
    <AdminAuthContext.Provider
      value={{ user, token, loading, busy, error, login, logout }}
    >
      {children}
    </AdminAuthContext.Provider>
  );
}

export function useAdminAuth() {
  return useContext(AdminAuthContext);
}