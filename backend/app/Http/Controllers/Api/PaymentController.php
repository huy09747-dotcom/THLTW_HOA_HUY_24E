<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Donhang;
use App\Services\SandboxPayment;
use Illuminate\Http\Request;
class PaymentController extends Controller {
 public function methods(SandboxPayment $service){return response()->json(['data'=>collect(['cod'=>'Thanh toán khi nhận hàng','momo'=>'MoMo (Sandbox)','paypal'=>'PayPal (Sandbox, USD)','vnpay'=>'Thẻ nội địa / VNPAY (Sandbox)'])->map(fn($label,$id)=>['id'=>$id,'label'=>$label,'enabled'=>$service->configured($id)])->values()]);}
 public function start(Request $r,int $id,SandboxPayment $service){$order=Donhang::where('user_id',$r->user()->id)->findOrFail($id);return response()->json(['data'=>$service->start($order,$r->ip())]);}
 public function verify(Request $r,int $id,SandboxPayment $service){$order=Donhang::where('user_id',$r->user()->id)->findOrFail($id);if($order->payment_method==='paypal' && $order->payment_status==='unpaid')$service->verifyPaypal($order);return response()->json(['data'=>['payment_status'=>$order->fresh()->payment_status]]);}
 public function momo(Request $r,SandboxPayment $service){$payload=json_decode($r->getContent(),true);abort_unless(is_array($payload),400);$service->momo($payload);return response()->noContent();}
 public function vnpay(Request $r,SandboxPayment $service){parse_str($r->getQueryString() ?? '',$payload);return response()->json($service->vnpay($payload));}
}
