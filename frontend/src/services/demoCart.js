import { demoProducts } from "@/data/demoCatalog";

const KEY = "hh-shop-demo-cart";

export function readDemoCart() {
  let saved = [];
  try {
    saved = JSON.parse(localStorage.getItem(KEY) || "[]");
  } catch {
    saved = [];
  }
  if (!Array.isArray(saved)) saved = [];
  const data = saved.flatMap((row) => {
    const product = demoProducts.find((item) => item.id === Number(row.product_id));
    if (!product || !Number.isInteger(row.quantity) || row.quantity < 1) return [];
    const quantity = Math.min(row.quantity, product.qty);
    if (!quantity) return [];
    return [
      {
        id: product.id,
        product_id: product.id,
        name: product.name,
        image: product.image,
        price: product.price_sale,
        quantity,
        stock: product.qty,
        available: true,
        subtotal: quantity * product.price_sale,
      },
    ];
  });
  return {
    data,
    count: data.reduce((sum, item) => sum + item.quantity, 0),
    total: data.reduce((sum, item) => sum + item.subtotal, 0),
  };
}

export function updateDemoCart(path, method, body) {
  const cart = readDemoCart();
  const id = method === "POST" ? Number(body.product_id) : Number(path.split("/").pop());
  const product = demoProducts.find((item) => item.id === id);
  if (!product) throw new Error("San pham khong ton tai.");
  const existing = cart.data.find((item) => item.id === id);
  if (method === "DELETE") {
    cart.data = cart.data.filter((item) => item.id !== id);
  } else {
    const quantity =
      Number(body.quantity) + (method === "POST" ? existing?.quantity || 0 : 0);
    if (
      !Number.isInteger(quantity) ||
      quantity < 1 ||
      quantity > Math.min(999, product.qty)
    ) {
      throw new Error("So luong vuot qua ton kho.");
    }
    if (existing) existing.quantity = quantity;
    else cart.data.push({ product_id: id, quantity });
  }
  localStorage.setItem(
    KEY,
    JSON.stringify(
      cart.data.map(({ product_id, quantity }) => ({ product_id, quantity })),
    ),
  );
  return readDemoCart();
}
