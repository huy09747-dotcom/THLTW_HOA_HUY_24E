import Header from "@/components/shop/Header";
import Footer from "@/components/shop/Footer";
import CartProvider from "@/components/shop/CartProvider";

import { getSiteSettings } from "@/services/siteContent";

export async function generateMetadata() {
  const settings=await getSiteSettings();
  return {title:{default:settings.site_name,template:"%s | "+settings.site_name},description:settings.meta_description || ""};
}
export default async function ShopLayout({ children }) {
  const settings=await getSiteSettings();
  return (
    <CartProvider>
      <Header settings={settings} />
      <main id="main-content">{children}</main>
      <Footer settings={settings} />
    </CartProvider>
  );
}
