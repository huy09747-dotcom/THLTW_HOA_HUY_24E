import Link from "next/link";
import {notFound} from "next/navigation";
import {api} from "@/services/api";
import {ProductImage} from "@/components/shop/ProductCard";
export default async function NewsDetail({params}){const {slug}=await params;let post,error;try{post=(await api('/news/'+encodeURIComponent(slug))).data;}catch(e){if(e.status===404)notFound();error=e.message;}return <article className="container information-page"><Link className="news-action" href="/news">← Tin tức & sự kiện</Link>{error?<p role="alert">{error}</p>:<><p className="eyebrow">{post.topic_name}</p><h1>{post.title}</h1><p>{post.created_at?new Date(post.created_at).toLocaleDateString('vi-VN'):''}</p>{post.image&&<ProductImage image={post.image} name={post.title}/>}<p className="information-summary">{post.description}</p><div className="information-body">{post.detail}</div></>}</article>;}
