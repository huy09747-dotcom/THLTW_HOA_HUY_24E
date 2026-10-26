"use client";
import {useEffect,useState} from "react";
import Link from "next/link";
import {imageUrl} from "@/utils/format";
import styles from "./BannerCarousel.module.css";
export default function BannerCarousel({banners=[]}){
 const [index,setIndex]=useState(0),[hover,setHover]=useState(false),[focused,setFocused]=useState(false);
 useEffect(()=>{
  if(hover||focused||banners.length<2)return;
  const timer=setInterval(()=>{if(!document.hidden&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches)setIndex(i=>(i+1)%banners.length);},5000);
  return ()=>clearInterval(timer);
 },[hover,focused,banners.length]);
 if(!banners.length)return <section className={styles.empty}><h1>Khám phá công nghệ cùng HH Shop</h1><Link href="/products" className="primary-button">Khám phá sản phẩm ↗</Link></section>;
 const current=index%banners.length;
 return <section className={styles.carousel} aria-label="Banner nổi bật" aria-roledescription="trình chiếu" onMouseEnter={()=>setHover(true)} onMouseLeave={()=>setHover(false)} onFocusCapture={()=>setFocused(true)} onBlurCapture={e=>{if(!e.currentTarget.contains(e.relatedTarget))setFocused(false);}}>
  {banners.map((b,i)=><div key={b.id} className={styles.slide+' '+(i===current?styles.active:'')} aria-hidden={i!==current} inert={i!==current}>
   {/* eslint-disable-next-line @next/next/no-img-element */}
   <img className={styles.image} src={imageUrl(b.image)||undefined} alt="" fetchPriority={i===0?'high':'auto'}/>
   <div className={styles.copy}><span className={styles.eyebrow}>KHÁM PHÁ CÙNG HH SHOP</span><h1>{b.name}</h1><p>{b.description}</p>{b.link&&<Link className="primary-button" href={b.link}>Khám phá ngay <span aria-hidden="true">↗</span></Link>}</div>
  </div>)}
  {banners.length>1&&<>
   <button className={styles.arrow+' '+styles.previous} aria-label="Banner trước" onClick={()=>setIndex((current-1+banners.length)%banners.length)}>‹</button>
   <button className={styles.arrow+' '+styles.next} aria-label="Banner sau" onClick={()=>setIndex((current+1)%banners.length)}>›</button>
   <div className={styles.controls}><div className={styles.dots}>{banners.map((b,i)=><button key={b.id} aria-label={'Xem banner '+(i+1)} aria-pressed={i===current} className={i===current?styles.selected:''} onClick={()=>setIndex(i)}/>)}</div></div>
  </>}
 </section>;
}
