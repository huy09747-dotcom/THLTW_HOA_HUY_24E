/* eslint-disable @next/next/no-img-element -- Admin previews support uploaded image URLs. */
"use client";
import {useEffect,useState} from "react";
import {useAdminAuth} from "@/components/admin/AdminAuthProvider";
import {adminApi} from "@/services/adminApi";
import {imageUrl} from "@/utils/format";
import styles from "../admin.module.css";
const blank={name:'',description:'',image:'',link:'/products',sort_order:1,position:'slideshow',status:1};
export default function BannersAdmin(){
 const {token}=useAdminAuth();const [items,setItems]=useState(null),[form,setForm]=useState(blank),[editing,setEditing]=useState(null),[busy,setBusy]=useState(false),[error,setError]=useState(''),[notice,setNotice]=useState(''),[revision,setRevision]=useState(0);
 useEffect(()=>{if(!token)return;let active=true;adminApi('/admin/banners',token).then(r=>{if(active)setItems(r.data);}).catch(e=>{if(active)setError(e.message);});return()=>{active=false;};},[token,revision]);
 function change(e){setForm({...form,[e.target.name]:e.target.value});}
 async function upload(e){const file=e.target.files?.[0];if(!file)return;if(file.size>5*1024*1024){setError('Chọn ảnh không quá 5 MB.');return;}setBusy(true);setError('');try{const body=new FormData();body.append('image',file);const r=await adminApi('/images',token,{method:'POST',body});setForm(f=>({...f,image:r.image}));setNotice('Đã tải ảnh. Nhấn Lưu banner để áp dụng.');}catch(e){setError(e.message);}finally{setBusy(false);}}
 async function save(e){e.preventDefault();setBusy(true);setError('');setNotice('');try{await adminApi('/admin/banners'+(editing?'/'+editing:''),token,{method:editing?'PUT':'POST',body:{...form,status:Number(form.status),sort_order:Number(form.sort_order)}});setRevision(n=>n+1);setForm(blank);setEditing(null);setNotice('Đã lưu banner. Trang chủ sẽ cập nhật khi tải lại.');}catch(e){setError(e.message);}finally{setBusy(false);}}
 async function remove(item){if(!window.confirm('Xóa banner “'+item.name+'”?'))return;setBusy(true);setError('');try{await adminApi('/admin/banners/'+item.id,token,{method:'DELETE'});setItems(a=>a.filter(b=>b.id!==item.id));if(editing===item.id){setEditing(null);setForm(blank);}setNotice('Đã xóa banner.');}catch(e){setError(e.message);}finally{setBusy(false);}}
 return <div><h1 className={styles.title}>Banner</h1><p className={styles.description}>Quản lý ảnh trình chiếu trang chủ. Banner hiển thị theo thứ tự tăng dần, tự chuyển mỗi 5 giây.</p>{error&&<p role="alert" className={styles.error}>{error}</p>}{notice&&<p role="status">{notice}</p>}
 <form className={styles.form} onSubmit={save}><h2>{editing?'Chỉnh sửa banner':'Thêm banner'}</h2><fieldset disabled={busy} style={{border:0,padding:0,display:'grid',gap:12}}>
 <label htmlFor="banner-name">Tiêu đề</label><input id="banner-name" name="name" value={form.name} onChange={change} maxLength={255} required/>
 <label htmlFor="banner-description">Mô tả</label><textarea id="banner-description" name="description" value={form.description||''} onChange={change} maxLength={2000} rows={3}/>
 <label htmlFor="banner-upload">Tải ảnh từ máy (JPG, PNG, WebP, tối đa 5 MB)</label><input key={editing??'new'} id="banner-upload" type="file" accept="image/jpeg,image/png,image/webp" onChange={upload}/>
 <label htmlFor="banner-image">Đường dẫn ảnh</label><input id="banner-image" name="image" value={form.image} onChange={change} maxLength={255} required placeholder="/storage/images/... hoặc https://..."/>
 {imageUrl(form.image)&&<div><img src={imageUrl(form.image)} alt="Ảnh banner xem trước" style={{width:'100%',maxWidth:600,maxHeight:230,objectFit:'cover',borderRadius:12}}/></div>}
 <small>Ảnh ngang, chủ thể bên phải để chừa chỗ cho tiêu đề bên trái. Gợi ý kích thước 1920 × 800 px.</small>
 <label htmlFor="banner-link">Liên kết khi nhấn Khám phá ngay</label><input id="banner-link" name="link" value={form.link||''} onChange={change} maxLength={2048}/>
 <label htmlFor="banner-order">Thứ tự</label><input id="banner-order" type="number" name="sort_order" value={form.sort_order} onChange={change} min={0} max={9999} required/>
 <label htmlFor="banner-position">Vị trí</label><select id="banner-position" name="position" value={form.position} onChange={change}><option value="slideshow">Trình chiếu trang chủ</option><option value="advertise">Quảng cáo (không nằm trong trình chiếu)</option></select>
 <label htmlFor="banner-status">Trạng thái</label><select id="banner-status" name="status" value={form.status} onChange={change}><option value="1">Hiển thị</option><option value="0">Ẩn</option></select>
 <div className={styles.actions}><button className={styles.primaryButton}>{busy?'Đang xử lý…':'Lưu banner'}</button>{editing&&<button type="button" className={styles.button} onClick={()=>{setEditing(null);setForm(blank);}}>Hủy chỉnh sửa</button>}</div></fieldset></form>
 <h2 style={{marginTop:32}}>Danh sách banner</h2>{!items?error?<button className={styles.button} onClick={()=>setRevision(n=>n+1)}>Thử lại</button>:<p>Đang tải…</p>:items.length?<div className={styles.tableWrap}><table className={styles.table}><thead><tr><th>Ảnh</th><th>Tiêu đề</th><th>Thứ tự</th><th>Trạng thái / Vị trí</th><th>Thao tác</th></tr></thead><tbody>{items.map(b=><tr key={b.id}><td><img src={imageUrl(b.image)||undefined} alt={b.name} width={160} height={70} style={{objectFit:'cover',borderRadius:8}}/></td><td>{b.name}</td><td>{b.sort_order}</td><td>{b.status===1?'Hiển thị':'Ẩn'}<br/>{b.position==='slideshow'?'Trang chủ':'Quảng cáo'}</td><td><div className={styles.actions}><button disabled={busy} className={styles.button} onClick={()=>{setEditing(b.id);setForm(b);setNotice('');window.scrollTo({top:0,behavior:'smooth'});}}>Sửa</button><button disabled={busy} className={styles.button} onClick={()=>remove(b)}>Xóa</button></div></td></tr>)}</tbody></table></div>:<p>Chưa có banner. Thêm banner bằng biểu mẫu phía trên.</p>}</div>;
}
