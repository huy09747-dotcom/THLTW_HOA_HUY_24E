"use client";

import { createContext, useContext, useEffect, useState } from "react";
import { api } from "@/services/api";

import { USE_DEMO_CATALOG } from "@/data/demoCatalog";
import { readDemoCart, updateDemoCart } from "@/services/demoCart";

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
    setCart(USE_DEMO_CATALOG ? readDemoCart() : EMPTY_CART);
  }
  useEffect(() => {
    let active = true;
    async function restore() {
      try {
        if (USE_DEMO_CATALOG && active) setCart(readDemoCart());
        const saved = sessionStorage.getItem("hh-shop-token");
        if (!saved) return;
        const [account, basket] = await Promise.all([
          api("/user", { token: saved }),
          USE_DEMO_CATALOG
            ? Promise.resolve(readDemoCart())
            : api("/cart", { token: saved }),
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
  async function authenticate(path, body) {
    setBusy(true);
    setError("");
    try {
      const result = await api(path, { method: "POST", body });
      sessionStorage.setItem("hh-shop-token", result.token);
      setToken(result.token);
      setUser(result.user);
      try {
        setCart(
          USE_DEMO_CATALOG ? readDemoCart() : await api("/cart", { token: result.token }),
        );
      } catch (cartError) {
        setError(cartError.message);
      }
      return true;
    } catch (e) {
      setError(e.message);
      return false;
    } finally {
      setBusy(false);
    }
  }
  function login(email, password) {
    return authenticate("/login", { email, password });
  }
  function register(body) {
    return authenticate("/register", body);
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
      setCart(
        USE_DEMO_CATALOG
          ? updateDemoCart(path, method, body)
          : await api(path, { method, body, token }),
      );
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
      value={{ user, cart, loading, busy, error, login, register, logout, changeCart }}
    >
      {children}
    </CartContext.Provider>
  );
}
