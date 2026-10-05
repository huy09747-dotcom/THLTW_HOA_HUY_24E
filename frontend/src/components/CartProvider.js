"use client";

import { createContext, useContext, useEffect, useState } from "react";
import { api } from "@/services/api";

const CartContext = createContext(null);
const EMPTY_CART = { data: [], total: 0, count: 0 };
export function useCart() {
  return useContext(CartContext);
}
export default function CartProvider({ children }) {
  const [token, setToken] = useState(null);
  const [user, setUser] = useState(null);
  const [cart, setCart] = useState(EMPTY_CART);
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  function clearSession() {
    sessionStorage.removeItem("hh-shop-token");
    setToken(null);
    setUser(null);
    setCart(EMPTY_CART);
  }
  useEffect(() => {
    let active = true;
    async function restore() {
      try {
        const saved = sessionStorage.getItem("hh-shop-token");
        if (!saved) return;
        const [account, basket] = await Promise.all([
          api("/user", { token: saved }),
          api("/cart", { token: saved }),
        ]);
        if (active) {
          setToken(saved);
          setUser(account);
          setCart(basket);
        }
      } catch (e) {
        if (active) {
          setError(e.message);
          if (e.status === 401 || e.status === 403)
            sessionStorage.removeItem("hh-shop-token");
        }
      } finally {
        if (active) setLoading(false);
      }
    }
    restore();
    return () => {
      active = false;
    };
  }, []);
  async function login(email, password) {
    setBusy(true);
    setError("");
    try {
      const result = await api("/login", { method: "POST", body: { email, password } });
      sessionStorage.setItem("hh-shop-token", result.token);
      setToken(result.token);
      setUser(result.user);
      setCart(await api("/cart", { token: result.token }));
      return true;
    } catch (e) {
      setError(e.message);
      return false;
    } finally {
      setBusy(false);
    }
  }
  async function logout() {
    setBusy(true);
    setError("");
    try {
      await api("/logout", { method: "POST", token });
      clearSession();
    } catch (e) {
      if (e.status === 401) clearSession();
      else setError(e.message);
    } finally {
      setBusy(false);
    }
  }
  async function changeCart(path, method, body) {
    setBusy(true);
    setError("");
    try {
      setCart(await api(path, { method, body, token }));
      return true;
    } catch (e) {
      if (e.status === 401) clearSession();
      setError(e.message);
      return false;
    } finally {
      setBusy(false);
    }
  }
  return (
    <CartContext.Provider
      value={{ user, cart, loading, busy, error, login, logout, changeCart }}
    >
      {children}
    </CartContext.Provider>
  );
}
