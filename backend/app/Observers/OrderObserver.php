<?php

namespace App\Observers;

use App\Mail\OrderStatusChanged;
use App\Models\Donhang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    public function created(Donhang $order): void
    {
        DB::table('order_status_history')->insert(['order_id'=>$order->id,'from_status'=>null,'to_status'=>$order->status,'actor_id'=>auth()->id(),'created_at'=>now()]);
    }

    public function updated(Donhang $order): void
    {
        if ($order->wasChanged('status')) {
            DB::table('order_status_history')->insert(['order_id'=>$order->id,'from_status'=>$order->getRawOriginal('status'),'to_status'=>$order->status,'actor_id'=>auth()->id(),'created_at'=>now()]);
        }
        if (!$order->wasChanged(['status','payment_status']) || !$order->email) return;
        $snapshot=['guest_session_id'=>$order->guest_session_id,'id'=>$order->id,'name'=>$order->name,'status'=>(int)$order->status,'payment_status'=>$order->payment_status,'email'=>$order->email];
        DB::afterCommit(function () use ($snapshot) {
            try { Mail::to($snapshot['email'])->queue(new OrderStatusChanged($snapshot)); }
            catch (\Throwable $e) { report($e); }
        });
    }
}
