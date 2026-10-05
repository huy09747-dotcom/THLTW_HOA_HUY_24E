import { api, getAllPages } from "./api";

export async function getProducts() {
  return (await getAllPages("/products")).filter((product) => Number(product.status) === 1);
}

export async function getProduct(slug) {
  return (await api("/products/" + encodeURIComponent(slug))).data;
}
