<?php

namespace App\Mail;

use App\Models\Donhang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donhang $order)
    {
        $this->onConnection('database');
    }

    public function build()
    {
        $displayCode = 'HH-'.str_pad((string) $this->order->id, 6, '0', STR_PAD_LEFT);
        return $this->subject('HH Shop - Xác nhận đơn hàng '.$displayCode)
            ->view('emails.order-confirmation', [
                'order' => $this->order->load('items'),
                'displayCode' => $displayCode,
            ]);
    }
}
