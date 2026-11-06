<?php

namespace App\Services;

use App\Models\Donhang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PayosPayment
{
    private const BASE = 'https://api-merchant.payos.vn';

    private function client()
    {
        abort_unless(config('payments.payos.client') && config('payments.payos.key')
            && config('payments.payos.checksum'), 422, 'payOS chua duoc cau hinh.');

        return Http::connectTimeout(5)->timeout(15)->acceptJson()->withHeaders([
            'x-client-id' => config('payments.payos.client'),
            'x-api-key' => config('payments.payos.key'),
        ]);
    }

    public function signature(array $data): string
    {
        ksort($data);
        $parts = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === 'null' || $value === 'undefined') {
                $value = '';
            } elseif (is_array($value)) {
                $value = array_map(function ($item) {
                    if (is_array($item)) ksort($item);
                    return $item;
                }, $value);
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $parts[] = $key.'='.$value;
        }

        return hash_hmac('sha256', implode('&', $parts), (string) config('payments.payos.checksum'));
    }

    public function start(Donhang $order, ?string $origin = null): array
    {
        $base = rtrim(config('payments.frontend'), '/');
        $allowed = array_merge([$base], config('payments.frontend_origins', []));
        if (in_array(parse_url($base, PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) {
            $allowed[] = str_replace('localhost', '127.0.0.1', $base);
            $allowed[] = str_replace('127.0.0.1', 'localhost', $base);
        }
        abort_if($origin !== null && !in_array($origin, $allowed, true), 422, 'Dia chi quay ve khong hop le.');
        $base = $origin ?? $base;
        $client = $this->client();
        $attempt = DB::transaction(function () use ($order) {
            $locked = Donhang::whereKey($order->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->payment_method === 'payos' && $locked->payment_status === 'unpaid'
                && (int) $locked->status !== 5, 422, 'Don khong the thanh toan.');
            $amount = (int) $locked->total;
            abort_unless($amount > 0 && (float) $locked->total === (float) $amount,
                422, 'So tien thanh toan khong hop le.');
            $existing = DB::table('payment_attempts')->where('order_id', $locked->id)->first();
            if ($existing) {
                abort_unless($existing->method === 'payos' && (int) $existing->amount === $amount,
                    409, 'Thong tin thanh toan da thay doi.');
                return $existing;
            }
            $id = DB::table('payment_attempts')->insertGetId([
                'order_id' => $locked->id,
                'reference' => (string) random_int(1000000000000, 9000000000000),
                'method' => 'payos',
                'amount' => $amount,
                'currency' => 'VND',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return DB::table('payment_attempts')->find($id);
        });

        if ($attempt->url) {
            $response = $client->get(self::BASE.'/v2/payment-requests/'.$attempt->reference);
            abort_unless($response->successful() && $response->json('code') === '00',
                502, 'Chua kiem tra duoc lien ket thanh toan.');
            if (in_array($response->json('data.status'), ['CANCELLED', 'EXPIRED'], true)) {
                DB::table('payment_attempts')->where('id', $attempt->id)
                    ->where('reference', $attempt->reference)->where('status', 'pending')->update([
                        'reference' => (string) random_int(1000000000000, 9000000000000),
                        'url' => null, 'gateway_id' => null, 'updated_at' => now(),
                    ]);
                return $this->start($order, $origin);
            }
            return ['url' => $attempt->url, 'amount' => $attempt->amount, 'currency' => 'VND'];
        }

        $return = $base.($order->guest_session_id ? '/guest-orders/' : '/orders/').$order->id;
        $body = [
            'orderCode' => (int) $attempt->reference,
            'amount' => (int) $attempt->amount,
            'description' => 'HH'.$order->id,
            'cancelUrl' => $return,
            'returnUrl' => $return,
        ];
        $body['signature'] = $this->signature($body);
        // A stable orderCode lets a retry recover the same payment after a timeout.
        $response = $client->post(self::BASE.'/v2/payment-requests', $body);
        $payload = $response->json();
        if (!$response->successful() || ($payload['code'] ?? '') !== '00') {
            $existing = $this->client()->get(self::BASE.'/v2/payment-requests/'.$attempt->reference);
            $info = $existing->json('data');
            abort_unless($existing->successful() && $existing->json('code') === '00'
                && is_array($info) && (string) ($info['orderCode'] ?? '') === $attempt->reference
                && (int) ($info['amount'] ?? -1) === (int) $attempt->amount
                && preg_match('/^[a-zA-Z0-9-]+$/', $info['id'] ?? ''),
                502, 'Chua lay duoc giao dich payOS. Vui long thu lai.');
            $data = ['paymentLinkId' => $info['id'], 'checkoutUrl' => 'https://pay.payos.vn/web/'.$info['id']];
        } else {
            $data = $payload['data'] ?? null;
            abort_unless($response->successful() && ($payload['code'] ?? '') === '00'
                && is_array($data) && is_string($payload['signature'] ?? null)
                && hash_equals($this->signature($data), $payload['signature'])
                && (string) ($data['orderCode'] ?? '') === $attempt->reference
                && (int) ($data['amount'] ?? -1) === (int) $attempt->amount,
                502, 'Chua tao duoc thanh toan payOS. Kiem tra kenh thanh toan hoac thu lai.');
        }
        $url = $data['checkoutUrl'] ?? '';
        abort_unless(parse_url($url, PHP_URL_SCHEME) === 'https'
            && parse_url($url, PHP_URL_HOST) === 'pay.payos.vn'
            && is_string($data['paymentLinkId'] ?? null), 502, 'Lien ket payOS khong hop le.');
        DB::table('payment_attempts')->where('id', $attempt->id)->update([
            'url' => $url, 'gateway_id' => $data['paymentLinkId'], 'updated_at' => now(),
        ]);

        return ['url' => $url, 'amount' => $attempt->amount, 'currency' => 'VND'];
    }

    public function verify(Donhang $order): void
    {
        $attempt = DB::table('payment_attempts')->where('order_id', $order->id)
            ->where('method', 'payos')->first();
        if (!$attempt || $attempt->status === 'paid') return;

        $response = $this->client()->get(self::BASE.'/v2/payment-requests/'.$attempt->reference);
        $data = $response->json('data');
        abort_unless($response->successful() && $response->json('code') === '00' && is_array($data),
            502, 'Chua kiem tra duoc thanh toan payOS.');
        abort_unless((string) ($data['orderCode'] ?? '') === $attempt->reference
            && (int) ($data['amount'] ?? -1) === (int) $attempt->amount
            && (!$attempt->gateway_id || ($data['id'] ?? '') === $attempt->gateway_id),
            409, 'Giao dich payOS khong khop.');
        if (($data['status'] ?? '') !== 'PAID') return;
        abort_unless((int) ($data['amountPaid'] ?? -1) === (int) $attempt->amount
            && (int) ($data['amountRemaining'] ?? -1) === 0, 409, 'So tien nhan khong khop.');

        DB::transaction(function () use ($attempt) {
            $locked = Donhang::whereKey($attempt->order_id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->payment_method === 'payos'
                && (float) $locked->total === (float) $attempt->amount, 409, 'Don hang khong khop.');
            if ($locked->payment_status === 'unpaid') {
                $locked->payment_status = 'paid';
                if ((int) $locked->status === 1) {
                    $locked->status = 2;
                }
                $locked->save();
            }
            DB::table('payment_attempts')->where('id', $attempt->id)->update([
                'status' => 'paid', 'updated_at' => now(),
            ]);
        });
    }

    public function webhook(array $payload): void
    {
        $this->client();
        $data = $payload['data'] ?? null;
        abort_unless(is_array($data) && is_string($payload['signature'] ?? null)
            && hash_equals($this->signature($data), $payload['signature']),
            400, 'Chu ky payOS khong hop le.');
        if (($data['code'] ?? '') !== '00') return;
        $attempt = DB::table('payment_attempts')->where('method', 'payos')
            ->where('reference', (string) ($data['orderCode'] ?? ''))->first();
        // A signed webhook used to validate the endpoint need not belong to a local order.
        if (!$attempt) return;
        abort_unless((string) ($data['amount'] ?? '') === (string) $attempt->amount
            && ($data['currency'] ?? '') === 'VND'
            && (!$attempt->gateway_id || ($data['paymentLinkId'] ?? '') === $attempt->gateway_id),
            400, 'Thong tin thanh toan khong khop.');
        $this->verify(Donhang::findOrFail($attempt->order_id));
    }
}
