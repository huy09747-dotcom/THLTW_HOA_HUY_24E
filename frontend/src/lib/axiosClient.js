import axios from "axios";

const serverApi = (
  process.env.LARAVEL_API_URL || "http://127.0.0.1:8000/api"
).replace(/\/$/, "");

const axiosClient = axios.create({
  baseURL: typeof window === "undefined" ? serverApi : "/api/laravel",
  timeout: 10000,
  headers: {
    Accept: "application/json",
  },
});

// Truyền token theo từng yêu cầu để tách phiên User và Admin.
// Ví dụ: axiosClient.get("/user", { token });
axiosClient.interceptors.request.use((config) => {
  if (config.token) {
    config.headers.set("Authorization", `Bearer ${config.token}`);
  }

  delete config.token;
  return config;
});

axiosClient.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const status = error.response?.status;
    const data = error.response?.data;
    const validationMessage = data?.errors
      ? Object.values(data.errors).flat()[0]
      : null;

    const message = !error.response
      ? "Khong ket noi duoc may chu. Vui long thu lai."
      : status >= 500
        ? "May chu tam thoi khong phan hoi. Vui long thu lai."
        : validationMessage || data?.message || "Yeu cau khong thanh cong.";

    const requestError = new Error(message);
    requestError.status = status;
    requestError.errors = data?.errors;
    return Promise.reject(requestError);
  },
);

export default axiosClient;