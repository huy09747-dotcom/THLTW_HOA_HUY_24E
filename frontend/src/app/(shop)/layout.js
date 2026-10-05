import Header from "@/components/shop/Header";
import Footer from "@/components/shop/Footer";
import CartProvider from "@/components/shop/CartProvider";

export default function ShopLayout({ children }) {
  return (
    <CartProvider>
      <Header />
      <main id="main-content">{children}</main>
      <Footer />
    </CartProvider>
  );
}
