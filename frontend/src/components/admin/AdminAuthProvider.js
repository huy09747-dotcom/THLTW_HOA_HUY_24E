"use client";

import { createContext, useContext, useEffect, useState } from "react";
import { api } from "@/services/api";
import { adminApi } from "@/services/adminApi";

const AdminAuthContext = createContext(null);
const TOKEN_KEY = "hh-shop-admin-token";

function isAdmin(user) {
  return user?.roles === "admin" && Number(user.status) === 1;
}

export default function AdminAuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState("");
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  function clearSession() {
    sessionStorage.removeItem(TOKEN_KEY);
    setToken("");
    setUser(null);
  }

  useEffect(() => {
    let active = true;

    async function restoreSession() {
      try {
        const savedToken = sessionStorage.getItem(TOKEN_KEY);

        if (savedToken) {
          const account = await adminApi("/user", savedToken);

          if (!isAdmin(account)) {
            throw new Error("Tai khoan khong co quyen quan tri hoac da bi khoa.");
          }

          if (active) {
            setToken(savedToken);
            setUser(account);
          }
        }
      } catch (exception) {
        if (active) {
          sessionStorage.removeItem(TOKEN_KEY);
          setError(exception.message);
        }
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    }

    restoreSession();

    return () => {
      active = false;
    };
  }, []);

  async function login(email, password) {
    setBusy(true);
    setError("");

    try {
      const result = await api("/login", {
        method: "POST",
        body: { email, password },
      });

      if (!isAdmin(result.user)) {
        // Thu hồi token vừa cấp cho tài khoản không có quyền Admin.
        await adminApi("/logout", result.token, { method: "POST" });
        throw new Error("Tai khoan khong co quyen quan tri.");
      }

      sessionStorage.setItem(TOKEN_KEY, result.token);
      setToken(result.token);
      setUser(result.user);
      return true;
    } catch (exception) {
      setError(exception.message);
      return false;
    } finally {
      setBusy(false);
    }
  }

  async function logout() {
    setBusy(true);
    setError("");

    try {
      await adminApi("/logout", token, { method: "POST" });
      clearSession();
    } catch (exception) {
      if (exception.status === 401) {
        clearSession();
      } else {
        setError(exception.message);
      }
    } finally {
      setBusy(false);
    }
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
