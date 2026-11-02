<?php

namespace App\Observers;

use App\Mail\OrderStatusChanged;
use App\Models\Donhang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    public function updated(Donhang $order): void
    {
        if (!$order->wasChanged(['status','payment_status']) || !$order->email) return;
        $snapshot=['id'=>$order->id,'name'=>$order->name,'status'=>(int)$order->status,'payment_status'=>$order->payment_status,'email'=>$order->email];
        DB::afterCommit(function () use ($snapshot) {
            try { Mail::to($snapshot['email'])->queue(new OrderStatusChanged($snapshot)); }
            catch (\Throwable $e) { report($e); }
        });
    }
}
