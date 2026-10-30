<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Donhang;
use App\Services\SandboxPayment;
use App\Services\PayosPayment;
use Illuminate\Http\Request;
class PaymentController extends Controller {
 public function methods(SandboxPayment $service){return response()->json(['data'=>collect(['cod'=>'Thanh toán khi nhận hàng','payos'=>'Chuyển khoản ngân hàng'])->map(fn($label,$id)=>['id'=>$id,'label'=>$label,'enabled'=>$service->configured($id)])->values()]);}
 public function start(Request $r,int $id,SandboxPayment $service){$r->validate(['return_origin'=>'sometimes|nullable|string|max:255']);$order=Donhang::where('user_id',$r->user()->id)->findOrFail($id);return response()->json(['data'=>($order->payment_method === 'payos' ? app(PayosPayment::class)->start($order, $r->input('return_origin')) : $service->start($order,$r->ip()))]);}
 public function verify(Request $r,int $id,SandboxPayment $service){$order=Donhang::where('user_id',$r->user()->id)->findOrFail($id);if($order->payment_method==='paypal' && $order->payment_status==='unpaid')$service->verifyPaypal($order);if($order->payment_method==='payos' && $order->payment_status==='unpaid')app(PayosPayment::class)->verify($order);return response()->json(['data'=>['payment_status'=>$order->fresh()->payment_status]]);}
 public function payos(Request $request, PayosPayment $service)
 {
     $service->webhook($request->json()->all());
     return response()->json(['success' => true]);
 }

 public function momo(Request $r,SandboxPayment $service){$payload=json_decode($r->getContent(),true);abort_unless(is_array($payload),400);$service->momo($payload);return response()->noContent();}
 public function vnpay(Request $r,SandboxPayment $service){parse_str($r->getQueryString() ?? '',$payload);return response()->json($service->vnpay($payload));}
}
