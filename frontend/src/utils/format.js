
export function formatPrice(price) {
  return price == null
    ? "Liên hệ"
    : new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(Number(price));
}

// Chuyển đường dẫn ảnh API trả về thành URL có thể hiển thị trên frontend.
export function imageUrl(image) {
  if (!image) return null;

  const value = String(image).trim();

  // API trả về URL đầy đủ: http:// hoặc https://
  if (/^https?:\/\//i.test(value)) {
    return value;
  }

  // API trả về đường dẫn dạng /storage/images/...
  if (value.startsWith("/") && !value.startsWith("//")) {
    const origin =
      process.env.NEXT_PUBLIC_LARAVEL_ORIGIN || "http://127.0.0.1:8000";

    return `${origin.replace(/\/$/, "")}${value}`;
  }

  return null;
}
