import { api, getAllPages } from "./api";

export async function getProducts() {
  return (await getAllPages("/products")).filter((product) => Number(product.status) === 1);
}

export async function getProduct(slug) {
  return (await api("/products/" + encodeURIComponent(slug))).data;
}

export function getProductPage(params = {}) {
  const query = new URLSearchParams({ ...params, status: "1" });
  return api(`/products?${query}`);
}

export async function getRelatedProducts(product) {
  if (!product.category_id) return [];

  const result = await getProductPage({
    category_id: String(product.category_id),
    page: "1",
  });

  return result.data
    .filter((item) => String(item.id) !== String(product.id))
    .slice(0, 5);
}
