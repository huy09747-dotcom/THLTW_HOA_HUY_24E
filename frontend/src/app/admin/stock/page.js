"use client";
import { useEffect,useState } from "react";
import { useAdminAuth } from "@/components/admin/AdminAuthProvider";
import { adminApi } from "@/services/adminApi";
import { formatPrice } from "@/utils/format";
import styles from "../admin.module.css";

export default function StockPage(){
  const {token}=useAdminAuth();
  const [page,setPage]=useState(1),[query,setQuery]=useState(""),[revision,setRevision]=useState(0),[state,setState]=useState({});
  const [products,setProducts]=useState([]),[productSearch,setProductSearch]=useState(""),[productQuery,setProductQuery]=useState("");
  const [lines,setLines]=useState([{product_id:"",qty:1,unit_cost:0}]);
  const [busy,setBusy]=useState(false),[error,setError]=useState(""),[notice,setNotice]=useState("");
  useEffect(()=>{if(!token)return;let active=true;
    adminApi(`/admin/stock?page=${page}&${query}`,token).then(r=>{if(active)setState({result:r,page,query,revision});}).catch(e=>{if(active)setState({error:e.message,page,query,revision});});
    return()=>{active=false;};
  },[token,page,query,revision]);
  useEffect(()=>{if(!token)return;let active=true;
    adminApi(`/admin/stock/products?search=${encodeURIComponent(productQuery)}`,token).then(r=>{if(active)setProducts(r.data);}).catch(e=>{if(active)setError(e.message);});
    return()=>{active=false;};
  },[token,productQuery,revision]);
  async function save(e){
    e.preventDefault();if(!window.confirm('Ghi nhận phiếu và cập nhật tồn kho? Phiếu đã ghi không sửa hoặc xóa.'))return;
    const form=e.currentTarget;setBusy(true);setError('');setNotice('');
    try{const r=await adminApi('/admin/stock',token,{method:'POST',body:{...Object.fromEntries(new FormData(form)),items:lines}});setNotice(r.message);setLines([{product_id:'',qty:1,unit_cost:0}]);form.reset();setPage(1);setRevision(n=>n+1);}
    catch(e){setError(e.message);}finally{setBusy(false);}
  }
  function change(index,key,value){setLines(current=>current.map((row,i)=>i===index?{...row,[key]:value}:row));}
  const pending=state.page!==page||state.query!==query||state.revision!==revision;
  return <section><h1 className={styles.title}>Nhập / xuất kho</h1>
    <form className="feature-panel feature-actions" onSubmit={e=>{e.preventDefault();setProductQuery(productSearch);}}>
      <input value={productSearch} onChange={e=>setProductSearch(e.target.value)} maxLength={255} placeholder="Tìm sản phẩm để lập phiếu" aria-label="Tìm sản phẩm"/><button>Tìm sản phẩm</button>
    </form>
    <form className="feature-panel" onSubmit={save}><h2>Lập phiếu kho</h2>
      <div className="feature-actions"><label>Loại phiếu<select name="type"><option value="in">Nhập hàng</option><option value="out">Xuất hàng ngoài bán hàng</option></select></label>
      <label>Lý do<input name="reason" required maxLength={1000} placeholder="Nhập từ nhà cung cấp / xuất trả / hủy hàng lỗi"/></label></div>
      <fieldset disabled={busy} style={{border:0,padding:0}}>
      {lines.map((row,index)=><div className="feature-actions" key={index}>
        <label>Sản phẩm<select required value={row.product_id} onChange={e=>change(index,'product_id',e.target.value)}><option value="">Chọn sản phẩm</option>{!products.some(p=>String(p.id)===String(row.product_id))&&row.product_id&&<option value={row.product_id}>Sản phẩm #{row.product_id}</option>}{products.map(p=><option key={p.id} value={p.id}>{p.name} · Tồn {p.qty}</option>)}</select></label>
        <label>Số lượng<input type="number" required min={1} max={1000000} value={row.qty} onChange={e=>change(index,'qty',e.target.value)}/></label>
        <label>Đơn giá chứng từ (đ)<input type="number" required min={0} max={1000000000} value={row.unit_cost} onChange={e=>change(index,'unit_cost',e.target.value)}/></label>
        {lines.length>1&&<button type="button" onClick={()=>setLines(rows=>rows.filter((_,i)=>i!==index))}>Bỏ dòng</button>}
      </div>)}
      <div className="feature-actions"><button type="button" disabled={lines.length>=50} onClick={()=>setLines(rows=>[...rows,{product_id:'',qty:1,unit_cost:0}])}>Thêm dòng</button><button className={styles.primaryButton}>Ghi nhận phiếu</button></div>
      </fieldset>
    </form>
    {error&&<p role="alert" className={styles.error}>{error}</p>}{notice&&<p role="status">{notice}</p>}
    <section className="feature-panel"><h2>Tồn kho hiện tại</h2><div className={styles.tableWrap}><table className={styles.table}><thead><tr><th>Sản phẩm</th><th>Tồn</th><th>Giá nhập hiện tại</th></tr></thead><tbody>{products.map(p=><tr key={p.id}><td>{p.name}</td><td>{p.qty}</td><td>{formatPrice(p.price_buy)}</td></tr>)}</tbody></table></div></section>
    <h2>Lịch sử phiếu nhập / xuất</h2>
    <form className="feature-panel feature-actions" onSubmit={e=>{e.preventDefault();setPage(1);setQuery(new URLSearchParams(new FormData(e.currentTarget)).toString());setRevision(n=>n+1);}}>
      <input name="search" maxLength={255} placeholder="Mã phiếu / lý do" aria-label="Tìm phiếu"/>
      <select name="type" aria-label="Loại phiếu"><option value="">Tất cả</option><option value="in">Nhập</option><option value="out">Xuất</option></select>
      <label>Từ<input type="date" name="from"/></label><label>Đến<input type="date" name="to"/></label><button>Lọc</button>
    </form>
    {pending?<p>Đang tải…</p>:state.error?<p role="alert">{state.error}</p>:state.result&&<>
      {state.result.data.map(doc=><details className="feature-panel" key={doc.id}><summary>{doc.reference} · {doc.type==='in'?'Nhập':'Xuất'} · {doc.created_at} · {doc.reason}</summary><div className={styles.tableWrap}><table className={styles.table}><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá chứng từ</th><th>Tồn trước → sau</th></tr></thead><tbody>{doc.items.map(row=><tr key={row.id}><td>{row.product_name}</td><td>{row.qty}</td><td>{formatPrice(row.unit_cost)}</td><td>{row.stock_before} → {row.stock_after}</td></tr>)}</tbody></table></div></details>)}
      {!state.result.data.length&&<p>Chưa có phiếu.</p>}
      <div className={styles.pagination}><button className={styles.button} disabled={page<=1} onClick={()=>setPage(n=>n-1)}>Trang trước</button><span>{page} / {state.result.last_page}</span><button className={styles.button} disabled={page>=state.result.last_page} onClick={()=>setPage(n=>n+1)}>Trang sau</button></div>
    </>}
  </section>;
}
