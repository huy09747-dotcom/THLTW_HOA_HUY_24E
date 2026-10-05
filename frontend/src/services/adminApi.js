import { api } from "./api";

export function adminApi(path, token, options = {}) {
  if (!token) {
    const error = new Error("Ban chua Login");
    error.status = 401;
    return Promise.reject(error);
  }
  return api(path, { ...options, token });
}
