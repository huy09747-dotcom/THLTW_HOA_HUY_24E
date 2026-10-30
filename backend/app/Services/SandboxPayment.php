<?php
namespace App\Services;

use App\Models\Donhang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SandboxPayment
{
    public function configured(string $method): bool
    {
        $keys = match ($method) {
            'cod'=>[], 'payos'=>['client','key','checksum'], 'momo'=>['partner','access','secret'],
            'paypal'=>['client','secret','vnd_per_usd'], 'vnpay'=>['tmn','secret'], default=>['invalid'],
        };
        foreach ($keys as $key) if (!config("payments.$method.$key")) return false;
        return $method !== 'paypal' || config('payments.paypal.vnd_per_usd') > 0;
    }
    public function ensureConfigured(string $method): void
    {
        if (!$this->configured($method)) throw ValidationException::withMessages(['payment_method'=>'Phương thức chưa được cấu hình.']);
    }
    private function sign(array $data, string $secret): string
    {
        ksort($data);
        return hash_hmac('sha256', implode('&', array_map(fn($k)=>$k.'='.$data[$k],array_keys($data))),$secret);
    }
    private function paypal()
    {
        $token=Http::timeout(8)->asForm()->withBasicAuth(config('payments.paypal.client'),config('payments.paypal.secret'))
            ->post('https://api-m.sandbox.paypal.com/v1/oauth2/token',['grant_type'=>'client_credentials'])->throw()->json('access_token');
        abort_unless(is_string($token) && $token !== '',502,'Không nhận được token PayPal.');
        return Http::timeout(8)->withToken($token)->acceptJson();
    }
    public function start(Donhang $order, string $ip): array
    {
        if ($order->payment_method === 'payos') return app(PayosPayment::class)->start($order);
        $method=$order->payment_method;
        $this->ensureConfigured($method);
        abort_if($method==='cod' || $order->payment_status!=='unpaid' || (int)$order->status===5,422,'Đơn không thể thanh toán online.');
        abort_if((int)$order->total<=0,422,'Số tiền không hợp lệ.');
        // A stable reference per order prevents creating multiple chargeable sessions on retries.
        $attempt=DB::transaction(function() use($order,$method){
            Donhang::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $existing=DB::table('payment_attempts')->where('order_id',$order->id)->first();
            if($existing)return $existing;
            $usd=$method==='paypal';
            $amount=$usd ? (int)round($order->total/config('payments.paypal.vnd_per_usd')*100) : (int)$order->total;
            abort_if($amount<1,422,'Số tiền quy đổi quá nhỏ.');
            $id=DB::table('payment_attempts')->insertGetId(['order_id'=>$order->id,'reference'=>(string)Str::ulid(),'method'=>$method,'amount'=>$amount,'currency'=>$usd?'USD':'VND','status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
            return DB::table('payment_attempts')->find($id);
        });
        if($attempt->url)return ['url'=>$attempt->url,'amount'=>$attempt->amount,'currency'=>$attempt->currency];
        $return=rtrim(config('payments.frontend'),'/').'/orders/'.$order->id;
        if($method==='vnpay') {
            $params=['vnp_Version'=>'2.1.0','vnp_Command'=>'pay','vnp_TmnCode'=>config('payments.vnpay.tmn'),'vnp_Amount'=>$attempt->amount*100,'vnp_CurrCode'=>'VND','vnp_TxnRef'=>$attempt->reference,'vnp_OrderInfo'=>'Thanh toan '.$order->code,'vnp_OrderType'=>'other','vnp_Locale'=>'vn','vnp_BankCode'=>'VNBANK','vnp_ReturnUrl'=>$return,'vnp_IpAddr'=>$ip,'vnp_CreateDate'=>now('Asia/Ho_Chi_Minh')->format('YmdHis'),'vnp_ExpireDate'=>now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis')];
            ksort($params);$query=http_build_query($params,'','&',PHP_QUERY_RFC1738);
            $url='https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?'.$query.'&vnp_SecureHash='.hash_hmac('sha512',$query,config('payments.vnpay.secret'));
            $gateway=$attempt->reference;
        } elseif($method==='momo') {
            $body=['partnerCode'=>config('payments.momo.partner'),'requestId'=>$attempt->reference,'orderId'=>$attempt->reference,'amount'=>$attempt->amount,'orderInfo'=>'Thanh toan '.$order->code,'redirectUrl'=>$return,'ipnUrl'=>rtrim(config('payments.callback_base'),'/').'/api/payments/momo/ipn','extraData'=>'','requestType'=>'captureWallet'];
            $body['signature']=$this->sign($body+['accessKey'=>config('payments.momo.access')],config('payments.momo.secret'));
            $response=Http::timeout(8)->post('https://test-payment.momo.vn/v2/gateway/api/create',$body+['lang'=>'vi','autoCapture'=>true])->throw()->json();
            abort_unless(isset($response['resultCode']) && (int)$response['resultCode']===0,502,'MoMo chưa tạo được thanh toán.');
            $url=$response['payUrl'] ?? ''; $gateway=$attempt->reference;
        } else {
            $response=$this->paypal()->withHeaders(['PayPal-Request-Id'=>$attempt->reference])->post('https://api-m.sandbox.paypal.com/v2/checkout/orders',[
                'intent'=>'CAPTURE','purchase_units'=>[['reference_id'=>$attempt->reference,'custom_id'=>$attempt->reference,'amount'=>['currency_code'=>'USD','value'=>number_format($attempt->amount/100,2,'.','')]]],
                'payment_source'=>['paypal'=>['experience_context'=>['return_url'=>$return,'cancel_url'=>$return,'user_action'=>'PAY_NOW']]],
            ])->throw()->json();
            $gateway=$response['id'] ?? null;
            $url=collect($response['links'] ?? [])->first(fn($link)=>in_array($link['rel'],['payer-action','approve']))['href'] ?? '';
        }
        $host=parse_url($url,PHP_URL_HOST);
        abort_unless(parse_url($url,PHP_URL_SCHEME)==='https' && in_array($host,['sandbox.vnpayment.vn','test-payment.momo.vn','www.sandbox.paypal.com','sandbox.paypal.com']),502,'URL thanh toán không hợp lệ.');
        DB::table('payment_attempts')->where('id',$attempt->id)->update(['url'=>$url,'gateway_id'=>$gateway,'updated_at'=>now()]);
        return ['url'=>$url,'amount'=>$attempt->amount,'currency'=>$attempt->currency];
    }
    private function settle(object $attempt): void
    {
        DB::transaction(function()use($attempt){
            $order=Donhang::whereKey($attempt->order_id)->lockForUpdate()->firstOrFail();
            abort_unless($order->payment_method===$attempt->method,409,'Phương thức không khớp.');
            if($order->payment_status==='unpaid'){$order->payment_status='paid';$order->save();}
            DB::table('payment_attempts')->where('id',$attempt->id)->update(['status'=>'paid','updated_at'=>now()]);
        });
    }
    public function verifyPaypal(Donhang $order): void
    {
        $this->ensureConfigured('paypal');
        $a=DB::table('payment_attempts')->where('order_id',$order->id)->where('method','paypal')->first();
        abort_unless($a && $a->gateway_id,422,'Chưa tạo giao dịch PayPal.');
        $client=$this->paypal();$base='https://api-m.sandbox.paypal.com/v2/checkout/orders/'.rawurlencode($a->gateway_id);
        $data=$client->get($base)->throw()->json();
        if(($data['status']??'')==='APPROVED') {
            $client->withHeaders(['PayPal-Request-Id'=>'capture-'.$a->reference])->withBody('{}','application/json')->post($base.'/capture');
            $data=$this->paypal()->get($base)->throw()->json();
        }
        $unit=$data['purchase_units'][0]??[];$capture=$unit['payments']['captures'][0]??[];
        abort_unless(($data['id']??null)===$a->gateway_id && ($unit['custom_id']??null)===$a->reference && ($capture['status']??'')==='COMPLETED' && ($capture['amount']['currency_code']??'')==='USD' && (int)round((float)($capture['amount']['value']??0)*100)===(int)$a->amount,422,'PayPal chưa xác nhận thanh toán thành công.');
        $this->settle($a);
    }
    public function momo(array $data): void
    {
        $this->ensureConfigured('momo');
        $keys=['amount','extraData','message','orderId','orderInfo','orderType','partnerCode','payType','requestId','responseTime','resultCode','transId'];
        $signed=['accessKey'=>config('payments.momo.access')];
        foreach($keys as $key){abort_unless(array_key_exists($key,$data)&&is_scalar($data[$key]),400,'Thiếu dữ liệu.');$signed[$key]=$data[$key];}
        abort_unless(is_string($data['signature']??null)&&hash_equals($this->sign($signed,config('payments.momo.secret')),$data['signature']),400,'Chữ ký không hợp lệ.');
        abort_unless($data['partnerCode']===config('payments.momo.partner'),400);
        $a=DB::table('payment_attempts')->where('reference',$data['orderId'])->where('method','momo')->first();
        abort_unless($a && $data['requestId']===$a->reference && (string)$data['amount']===(string)$a->amount,400,'Giao dịch không khớp.');
        if((string)$data['resultCode']==='0')$this->settle($a);
    }
    public function vnpay(array $data): array
    {
        $this->ensureConfigured('vnpay');$signature=$data['vnp_SecureHash']??'';
        unset($data['vnp_SecureHash'],$data['vnp_SecureHashType']);
        $data=array_filter($data,fn($k)=>str_starts_with($k,'vnp_'),ARRAY_FILTER_USE_KEY);
        foreach($data as $value)if(!is_scalar($value))return ['RspCode'=>'97','Message'=>'Invalid data'];
        ksort($data);
        if(!is_string($signature)||!hash_equals(hash_hmac('sha512',http_build_query($data,'','&',PHP_QUERY_RFC1738),config('payments.vnpay.secret')),$signature))return ['RspCode'=>'97','Message'=>'Invalid signature'];
        if(($data['vnp_TmnCode']??'')!==config('payments.vnpay.tmn'))return ['RspCode'=>'97','Message'=>'Invalid merchant'];
        $a=DB::table('payment_attempts')->where('reference',$data['vnp_TxnRef']??'')->where('method','vnpay')->first();
        if(!$a)return ['RspCode'=>'01','Message'=>'Order not found'];
        if((string)($data['vnp_Amount']??'')!==(string)($a->amount*100))return ['RspCode'=>'04','Message'=>'Invalid amount'];
        if($a->status==='paid')return ['RspCode'=>'02','Message'=>'Already confirmed'];
        if(($data['vnp_ResponseCode']??'')==='00' && ($data['vnp_TransactionStatus']??'')==='00')$this->settle($a);
        return ['RspCode'=>'00','Message'=>'Confirm success'];
    }
}
