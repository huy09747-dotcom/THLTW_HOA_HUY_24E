import "./globals.css";

export const metadata = {
  icons: { icon: "/default-favicon.ico" },
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
        {children}
      </body>
    </html>
  );
}
