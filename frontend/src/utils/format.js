export function formatPrice(price) {
  return price == null
    ? "Liên hệ"
    : new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(
        Number(price),
      );
}
// Chỉ sử dụng URL/đường dẫn ảnh được API cung cấp, không đoán thư mục ảnh.
export function imageUrl(image) {
  if (!image) return null;
  if (/^https?:\/\//i.test(image)) return image;
  if (image.startsWith("/") && !image.startsWith("//")) {
    return `${process.env.NEXT_PUBLIC_LARAVEL_ORIGIN || "http://127.0.0.1:8000"}${image}`;
  }
  return null;
}

export function toSlug(text) {
  return String(text ?? "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/đ/g, "d")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}
