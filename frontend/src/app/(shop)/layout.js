import {api} from "@/services/api";
import Header from "@/components/shop/Header";
import Footer from "@/components/shop/Footer";
import CartProvider from "@/components/shop/CartProvider";

import { getSiteSettings } from "@/services/siteContent";

export async function generateMetadata() {
  const settings=await getSiteSettings();
  return {title:{default:settings.seo_title || settings.site_name,template:"%s | "+settings.site_name},description:settings.meta_description || "", keywords:settings.seo_keywords || undefined, ...(settings.favicon && /^https?:\/\//i.test(settings.favicon) ? {icons:{icon:settings.favicon}} : {})};
}
export default async function ShopLayout({ children }) {
  const menus=await api("/menus").then(r=>r.data).catch(()=>null);
  const settings=await getSiteSettings();
  return (
    <CartProvider>
      <Header settings={settings} menus={menus} />
      <main id="main-content">{children}</main>
      <Footer settings={settings} />
    </CartProvider>
  );
}
