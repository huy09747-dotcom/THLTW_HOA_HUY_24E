<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class OrderStatusChanged extends Mailable
{
    use Queueable;

    public function __construct(public array $snapshot)
    {
        $this->onConnection('database');
    }

    public function build()
    {
        $code='HH-'.str_pad((string)$this->snapshot['id'],6,'0',STR_PAD_LEFT);
        return $this->subject('HH Shop - Cập nhật đơn hàng '.$code)
            ->view('emails.order-status',['order'=>$this->snapshot,'code'=>$code]);
    }
}
