import { cache } from "react";
import { api } from "./api";
const defaults={site_name:"HH Shop",tagline:"Công nghệ mỗi ngày",footer_text:"Công nghệ gần hơn với bạn.",meta_description:"Khám phá sản phẩm công nghệ tại HH Shop."};
export const getSiteSettings=cache(async () => {
  try { return (await api("/site-settings")).data; } catch { return defaults; }
});
export const getPublicPage=cache(async (slug) => {
  try {return (await api("/pages/"+slug)).data;}
  catch(error) {if(error.status===404)return null;throw error;}
});
