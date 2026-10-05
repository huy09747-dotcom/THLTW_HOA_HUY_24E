import { api } from "@/services/api";

import { demoProducts, demoCategories } from "@/data/demoCatalog";

export function adminApi(path, token, options = {}) {
  if (token === "hh-shop-admin-preview") {
    if ((options.method || "GET") !== "GET") {
      return Promise.reject(new Error("Vui long dang nhap Admin de luu thay doi."));
    }
    const [endpoint, query] = path.split("?");
    const sources = {
      "/products": demoProducts,
      "/categories": demoCategories,
      "/categories-trash": [],
      "/brands": [{ id: 0, name: "HH Shop", slug: "hh-shop", status: 1 }],
    };
    if (!(endpoint in sources)) {
      return Promise.reject(new Error("Chua co du lieu xem thu."));
    }
    const items = sources[endpoint].map((item) => ({ status: 1, ...item }));
    const lastPage = Math.max(1, Math.ceil(items.length / 10));
    const page = Math.min(lastPage, Math.max(1, Number(new URLSearchParams(query).get("page")) || 1));
    return Promise.resolve({
      data: items.slice((page - 1) * 10, page * 10),
      meta: { current_page: page, last_page: lastPage, total: items.length },
    });
  }
  if (!token) {
    const error = new Error("Ban chua Login");
    error.status = 401;
    return Promise.reject(error);
  }

  return api(path, { ...options, token });
}
