"use client";
import {useEffect,useRef,useState,useId} from "react";
import ProductCard from "./ProductCard";
export default function ProductCarousel({products,label}) {
 const track=useRef(null);const id=useId();const [edges,setEdges]=useState({start:true,end:true});
 useEffect(()=>{
  const el=track.current;if(!el)return;
  function update(){const next={start:el.scrollLeft<2,end:el.scrollLeft+el.clientWidth>=el.scrollWidth-2};setEdges(old=>old.start===next.start&&old.end===next.end?old:next);}
  update();const observer=new ResizeObserver(update);observer.observe(el);el.addEventListener('scroll',update,{passive:true});return()=>{observer.disconnect();el.removeEventListener('scroll',update);};
 },[products.length]);
 function move(direction){const el=track.current;if(!el)return;const card=el.firstElementChild;const step=card?card.getBoundingClientRect().width+parseFloat(getComputedStyle(el).columnGap||0):el.clientWidth;el.scrollBy({left:direction*step,behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});}
 return <div className="product-carousel" role="region" aria-label={label}>
  <div ref={track} id={id} className="product-carousel-track" tabIndex={0} aria-label={'Danh sách '+label.toLowerCase()}>{products.map(product=><ProductCard key={product.id} product={product}/>)}</div>
  {!(edges.start&&edges.end)&&<><button className="product-carousel-arrow product-carousel-previous" type="button" aria-controls={id} aria-label={'Xem sản phẩm trước trong '+label.toLowerCase()} disabled={edges.start} onClick={()=>move(-1)}>‹</button><button className="product-carousel-arrow product-carousel-next" type="button" aria-controls={id} aria-label={'Xem sản phẩm tiếp theo trong '+label.toLowerCase()} disabled={edges.end} onClick={()=>move(1)}>›</button></>}
 </div>;
}
