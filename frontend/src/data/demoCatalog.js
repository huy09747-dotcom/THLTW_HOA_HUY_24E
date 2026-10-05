// Đổi thành false để trang User lấy lại dữ liệu Laravel.
export const USE_DEMO_CATALOG = true;

export const demoCategories = [
  { id: 1, name: "Điện thoại", parent_id: 0, image: null },
  { id: 2, name: "Laptop", parent_id: 0, image: null },
  { id: 3, name: "Phụ kiện", parent_id: 0, image: null },
  { id: 4, name: "Tai nghe", parent_id: 3, image: null },
  { id: 5, name: "Sạc và cáp", parent_id: 3, image: null },
];

const items = [
  ["Điện thoại HH Nova 128GB", 1, 5990000, 20, "phone", true],
  ["Điện thoại HH Nova Pro 256GB", 1, 8990000, 12, "phone", true],
  ["Điện thoại HH Mini 64GB", 1, 3490000, 0, "phone", false],
  ["Laptop HH Air 14 inch", 2, 15990000, 8, "laptop", true],
  ["Laptop HH Study 15 inch", 2, 11990000, 15, "laptop", false],
  ["Laptop HH Pro 16 inch", 2, 22990000, 5, "laptop", true],
  ["Tai nghe HH Buds không dây", 4, 790000, 35, "headphones", true],
  ["Tai nghe HH Studio", 4, 1290000, 10, "headphones", false],
  ["Củ sạc HH Fast 30W", 5, 290000, 50, "charger", false],
  ["Đế sạc HH không dây", 5, 99000, 60, "charger", false],
];

const productImages = [
  "smartphones/iphone-13-pro",
  "smartphones/oppo-f19-pro-plus",
  "smartphones/oppo-a57",
  "laptops/apple-macbook-pro-14-inch-space-grey",
  "laptops/huawei-matebook-x-pro",
  "laptops/asus-zenbook-pro-dual-screen-laptop",
  "mobile-accessories/apple-airpods",
  "mobile-accessories/apple-airpods-max-silver",
  "mobile-accessories/apple-iphone-charger",
  "mobile-accessories/apple-airpower-wireless-charger",
];

export const demoProducts = items.map(
  ([name, categoryId, price, qty, , featured], index) => ({
    id: 90001 + index,
    name,
    slug: "hh-demo-" + (index + 1),
    category_id: categoryId,
    category_name: demoCategories.find((category) => category.id === categoryId).name,
    brand_id: 0,
    brand_name: "HH Shop",
    price_buy: Math.round(price * 0.7),
    price_sale: price,
    qty,
    image:
      "https://cdn.dummyjson.com/product-images/" +
      productImages[index] +
      "/thumbnail.webp",
    detail:
      name +
      ". Thiết kế gọn gàng, dễ sử dụng trong công việc, học tập và giải trí hằng ngày.",
    description: "Thiết kế hiện đại, tiện dụng mỗi ngày.",
    status: 1,
    sku: "HH-" + (index + 1),
    unit: "cái",
    prep_minutes: 0,
    is_available: qty > 0 ? 1 : 0,
    is_featured: featured ? 1 : 0,
    is_demo: true,
  }),
);
