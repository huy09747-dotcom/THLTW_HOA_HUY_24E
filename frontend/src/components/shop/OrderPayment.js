"use client";
import {useState} from "react";
import {useCart} from "./CartProvider";
import {api} from "@/services/api";
const labels={cod:'Thanh toán khi nhận hàng',momo:'MoMo sandbox',paypal:'PayPal sandbox',vnpay:'Thẻ nội địa / VNPAY sandbox'};
export default function OrderPayment({order,onUpdated}) {
 const {token}=useCart();const [busy,setBusy]=useState(false);const [error,setError]=useState('');const [notice,setNotice]=useState('');const [offer,setOffer]=useState(null);
 async function action(verify=false){setBusy(true);setError('');setNotice('');try{const r=await api('/my-orders/'+order.id+'/payment'+(verify?'/verify':''),{method:'POST',token});if(verify){setNotice(r.data.payment_status==='paid'?'Đã xác nhận thanh toán.':'Chưa nhận được xác nhận thanh toán từ cổng.');onUpdated?.();}else{setOffer(r.data);}}catch(e){setError(e.message);}finally{setBusy(false);}}
 return <section className="commerce-panel"><h3>{labels[order.payment_method]||order.payment_method}</h3>{order.payment_method!=='cod'&&order.payment_status==='unpaid'&&Number(order.status)!==5&&<><p>Chế độ thử nghiệm. Sau khi thanh toán trên cổng, quay lại đây và kiểm tra trạng thái.</p><div className="auth-links"><button className="primary-button" disabled={busy} onClick={()=>action()}>Tạo liên kết thanh toán</button><button disabled={busy} onClick={()=>action(true)}>Kiểm tra thanh toán</button></div>{offer&&<p>{offer.currency==='USD'&&<>Số tiền sandbox: <strong>{(offer.amount/100).toFixed(2)} USD</strong> (quy đổi theo cấu hình cửa hàng). </>}<a className="primary-button" href={offer.url}>Đến cổng thanh toán ↗</a></p>}</>}{error&&<p role="alert" className="error-message">{error}</p>}{notice&&<p role="status">{notice}</p>}</section>;
}
