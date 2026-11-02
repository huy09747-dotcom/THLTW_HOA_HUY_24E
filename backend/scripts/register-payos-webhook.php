<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$url = $argv[1] ?? '';
if (parse_url($url, PHP_URL_SCHEME) !== 'https'
    || parse_url($url, PHP_URL_PATH) !== '/api/payments/payos/webhook') {
    fwrite(STDERR, "Can URL HTTPS ket thuc bang /api/payments/payos/webhook\n");
    exit(1);
}
try {
    $response = Illuminate\Support\Facades\Http::connectTimeout(5)->timeout(30)
        ->withHeaders([
            'x-client-id' => config('payments.payos.client'),
            'x-api-key' => config('payments.payos.key'),
        ])->post('https://api-merchant.payos.vn/confirm-webhook', ['webhookUrl' => $url]);
    if (!$response->successful() || $response->json('code') !== '00') {
        fwrite(STDERR, 'Dang ky chua thanh cong. HTTP '.$response->status().' / code '.
            (string) $response->json('code').PHP_EOL);
        exit(1);
    }
    echo "payOS da xac nhan webhook: ".$url.PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, "Khong ket noi duoc payOS. Kiem tra server va duong ham HTTPS.\n");
    exit(1);
}
