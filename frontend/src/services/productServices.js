import { api, getAllPages } from "./api";

export async function getProducts() {
  return (await getAllPages("/products")).filter((p) => Number(p.status) === 1);
}

export async function getProduct(id) {
  return (await api(`/products/${encodeURIComponent(id)}`)).data;
}
