<?php
namespace App\Mail;
use App\Models\Donhang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class OrderConfirmation extends Mailable {
 use Queueable, SerializesModels;
 public function __construct(public Donhang $order) {$this->onConnection('database');}
 public function build(){return $this->subject('Xác nhận đơn hàng '.$this->order->code)->view('emails.order-confirmation',['order'=>$this->order->load('items')]);}
}
