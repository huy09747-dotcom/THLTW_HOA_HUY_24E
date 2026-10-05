// Server Components gọi Laravel trực tiếp; trình duyệt đi qua rewrite của Next.js.
const SERVER_API = (process.env.LARAVEL_API_URL || "http://127.0.0.1:8000/api").replace(
  /\/$/,
  "",
);

export async function api(path, { token, method = "GET", body } = {}) {
  const base = typeof window === "undefined" ? SERVER_API : "/api/laravel";
  let response;
  try {
    response = await fetch(`${base}${path}`, {
      method,
      cache: "no-store",
      signal: AbortSignal.timeout(10000),
      headers: {
        Accept: "application/json",
        ...(body ? { "Content-Type": "application/json" } : {}),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
      ...(body ? { body: JSON.stringify(body) } : {}),
    });
  } catch {
    throw new Error("Chưa kết nối được cửa hàng. Vui lòng thử lại sau.");
  }
  const data = await response.json().catch(() => null);
  if (!response.ok) {
    if (response.status >= 500)
      throw new Error("Cửa hàng tạm thời không phản hồi. Vui lòng thử lại sau.");
    const message = data?.errors ? Object.values(data.errors).flat()[0] : data?.message;
    const error = new Error(message || "Không tải được dữ liệu. Vui lòng thử lại.");
    error.status = response.status;
    throw error;
  }
  if (!data) throw new Error("Dữ liệu cửa hàng không hợp lệ.");
  return data;
}

export async function getAllPages(path) {
  const first = await api(`${path}?page=1`);
  if (!Array.isArray(first.data)) throw new Error("Dữ liệu danh sách không hợp lệ.");
  const result = [...first.data];
  const last = Number(first.meta?.last_page || 1);
  for (let page = 2; page <= last; page++) {
    const next = await api(`${path}?page=${page}`);
    if (!Array.isArray(next.data)) throw new Error("Dữ liệu danh sách không hợp lệ.");
    result.push(...next.data);
  }
  return result;
}
