import { api, getAllPages } from "./api";
import { USE_DEMO_CATALOG, demoProducts } from "@/data/demoCatalog";

export async function getProducts() {
  if (USE_DEMO_CATALOG) {
    return demoProducts;
  }

  return (await getAllPages("/products")).filter(
    (product) => Number(product.status) === 1,
  );
}

export async function getProduct(id) {
  if (USE_DEMO_CATALOG) {
    const product = demoProducts.find((item) => String(item.id) === String(id));
    if (!product) {
      const error = new Error("Không tìm thấy sản phẩm mẫu.");
      error.status = 404;
      throw error;
    }
    return product;
  }

  return (await api("/products/" + encodeURIComponent(id))).data;
}
