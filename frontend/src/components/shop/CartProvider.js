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
  const [guestToken, setGuestToken] = useState(null);
  const [user, setUser] = useState(null);
  const [cart, setCart] = useState(EMPTY_CART);
  const [loading, setLoading] = useState(true);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [favoriteState, setFavoriteState] = useState({ token: null, ids: [] });
  const [favoriteBusy, setFavoriteBusy] = useState(false);
  const [favoriteError, setFavoriteError] = useState("");
  const favoriteIds = favoriteState.token === token ? favoriteState.ids : [];
  useEffect(() => {
    if (!token) return;
    let active = true;
    api("/favorites/ids", { token }).then(result => {
      if (active) setFavoriteState({ token, ids: result.data.map(Number) });
    }).catch(e => { if (active) setFavoriteError(e.message); });
    return () => { active = false; };
  }, [token]);

  async function toggleFavorite(id) {
    if (!token || favoriteBusy) return;
    setFavoriteBusy(true);
    setFavoriteError("");
    const selected = favoriteIds.includes(Number(id));
    try {
      await api(`/favorites/${id}`, { token, method: selected ? "DELETE" : "PUT" });
      setFavoriteState(previous => ({ token, ids: selected
        ? previous.ids.filter(value => value !== Number(id))
        : [...new Set([...previous.ids, Number(id)])] }));
    } catch (e) { setFavoriteError(e.message); }
    finally { setFavoriteBusy(false); }
  }

  function clearSession() {
    sessionStorage.removeItem("hh-shop-token");
    setToken(null);
    setUser(null);
    setCart(EMPTY_CART);
    // A full reload clears account-specific state and restores the guest cart.
    // eslint-disable-next-line @next/next/no-location-assign-relative-destination
    window.location.assign("/");
  }
  useEffect(() => {
    let active = true;
    async function restore() {
      try {
        const saved = sessionStorage.getItem("hh-shop-token");
        if (!saved) {
          let guest = sessionStorage.getItem("hh-guest-token");
          if (!guest) {
            guest = (await api("/guest/session", { method: "POST" })).token;
            sessionStorage.setItem("hh-guest-token", guest);
          }
          let basket;
          try { basket = await api("/guest/cart", { guestToken: guest }); }
          catch (e) {
            if (e.status !== 401) throw e;
            guest = (await api("/guest/session", { method: "POST" })).token;
            sessionStorage.setItem("hh-guest-token", guest);
            basket = await api("/guest/cart", { guestToken: guest });
          }
          if (active) { setGuestToken(guest); setCart(basket); }
          return;
        }
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
          await api("/cart", { token: result.token }),
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
    return authenticate("/login", { email, password, audience: "user" });
  }
  function register(body) {
    return authenticate("/register", body);
  }
  async function updateProfile(body) {
    setBusy(true);
    setError("");
    try {
      const result = await api("/profile", { method: "PUT", body, token });
      setUser(result.user);
      return true;
    } catch (e) {
      if (e.status === 401 || e.status === 403) clearSession();
      setError(e.message);
      return false;
    } finally { setBusy(false); }
  }
  async function logout() {
    setBusy(true);
    setError("");
    try {
      await api("/logout", { method: "POST", token });
      clearSession();
    } catch (e) {
      if (e.status === 401 || e.status === 403) clearSession();
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
        await api(token ? path : `/guest${path}`, { method, body, token, guestToken }),
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
  async function checkout(body) {
    if (busy) return null;
    setBusy(true);
    setError("");
    try {
      const result = await api(token ? "/checkout" : "/guest/checkout", { method: "POST", body, token, guestToken });
      setCart(EMPTY_CART);
      return result.data;
    } catch (e) {
      if (e.status === 401) clearSession();
      setError(e.message);
      return null;
    } finally {
      setBusy(false);
    }
  }
  return (
    <CartContext.Provider
      value={{ guestToken, favoriteIds, favoriteBusy, favoriteError, toggleFavorite, user, token, cart, loading, busy, error, login, register, logout, updateProfile, changeCart, checkout }}
    >
      {children}
    </CartContext.Provider>
  );
}
