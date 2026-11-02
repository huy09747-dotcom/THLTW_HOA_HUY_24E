import { notFound } from "next/navigation";
import ContactForm from "./ContactForm";
import Link from "next/link";
import { getPublicPage, getSiteSettings } from "@/services/siteContent";
export default async function ContentPage({slug, title}) {
  let page;
  try {page=await getPublicPage(slug);}catch {return <section className="container information-page"><h1>{title}</h1><p role="alert">Chưa tải được nội dung. Vui lòng thử lại sau.</p><Link href={"/"+slug}>Tải lại trang</Link></section>;}
  if (!page) notFound();
  const settings=slug==="contact" ? await getSiteSettings() : null;
  return <section className="container information-page">
    <p className="eyebrow">THÔNG TIN CỬA HÀNG</p><h1>{page?.title || title}</h1>
    {page ? <article>{page.description && <p className="information-summary">{page.description}</p>}<div className="information-body">{page.detail}</div></article> : <p>Nội dung đang được cập nhật.</p>}
    {settings && <aside className="contact-information" aria-label="Thông tin liên hệ"><h2>{settings.site_name}</h2>
      {settings.contact_email && <p>Email: <a href={"mailto:"+settings.contact_email}>{settings.contact_email}</a></p>}
      {settings.contact_phone && <p>Điện thoại: <a href={"tel:"+settings.contact_phone.replace(/[^+0-9]/g,"")}>{settings.contact_phone}</a></p>}
      {settings.address && <p>Địa chỉ: {settings.address}</p>}{settings.opening_hours && <p>Giờ làm việc: {settings.opening_hours}</p>}
    </aside>}
    {slug === "contact" && <ContactForm />}
  </section>;
}
