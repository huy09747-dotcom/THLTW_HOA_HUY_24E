import Header from "@/components/Header";
import Footer from "@/components/Footer";
import CartProvider from "@/components/CartProvider";
import "./globals.css";

export const metadata = {
  title: "HH Shop | Công nghệ mỗi ngày",
  description: "Khám phá sản phẩm công nghệ tại HH Shop.",
};

export default function RootLayout({ children }) {
  return (
    <html lang="vi">
      <body>
        <a
          className="skip-link"
          href="#main-content"
        >
          Đến nội dung chính
        </a>
        <CartProvider>
          <Header />
          <main id="main-content">{children}</main>
          <Footer />
        </CartProvider>
      </body>
    </html>
  );
}
