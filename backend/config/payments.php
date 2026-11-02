<?php
return [
 'frontend'=>env('FRONTEND_URL','http://localhost:3000'),
 'frontend_origins'=>array_values(array_filter(array_map('trim', explode(',', env('FRONTEND_ALLOWED_ORIGINS', ''))))),
 'callback_base'=>env('PAYMENT_CALLBACK_BASE',env('APP_URL','http://localhost:8000')),
 'payos'=>['client'=>env('PAYOS_CLIENT_ID'),'key'=>env('PAYOS_API_KEY'),'checksum'=>env('PAYOS_CHECKSUM_KEY')],
 'paypal'=>['client'=>env('PAYPAL_CLIENT_ID'),'secret'=>env('PAYPAL_CLIENT_SECRET'),'vnd_per_usd'=>(float)env('PAYPAL_VND_PER_USD',0)],
 'momo'=>['partner'=>env('MOMO_PARTNER_CODE'),'access'=>env('MOMO_ACCESS_KEY'),'secret'=>env('MOMO_SECRET_KEY')],
 'vnpay'=>['tmn'=>env('VNPAY_TMN_CODE'),'secret'=>env('VNPAY_HASH_SECRET')],
];
