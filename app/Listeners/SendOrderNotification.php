<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;


class SendOrderNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Redis::incr('reserved_count');
//        $order = $event->order;
//
//        // 在这里编写发送短信的逻辑，可以调用短信发送服务或第三方短信平台的 API
//
//        // 示例：假设使用第三方短信平台的 SDK 发送短信
//        $phoneNumber = $order->user->phone; // 假设用户手机号存储在 user 表中
//        $message = '尊敬的用户，您的订单已生成，订单号：' . $order->order_number;
        // 调用第三方短信平台的 SDK 发送短信
        // SmsService::send($phoneNumber, $message);
    }
}
