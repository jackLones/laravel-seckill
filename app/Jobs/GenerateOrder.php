<?php
/**
 * php artisan make:job UpdateProduct
 */

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Exceptions\InternalException;
use App\Models\Activity;
use App\Models\Order;
use App\Models\OrderRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class GenerateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 可以尝试任务的次数。
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * 失败前允许的最大未处理异常数。
     *
     * @var int
     */
    public int $maxExceptions = 3;

    private $data;
    /**
     * UpdateProduct constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 服务消费者会走到这里，把消息消费掉
     * php artisan queue:work rabbitmq
     * @throws \Exception
     */
    public function handle()
    {
        $data = $this->data;
        print_r($data);
        // 启动事务
        DB::beginTransaction();
        // 捕获异常
        try{
            // 数据初始化
            $order = [];
            $order_id = "sec".app('snowflake')->nextId();
            $order['order_id'] = config('constants.pre_sec_order_id').$order_id;
            $order['product_id'] = $data['product_id'];
            $order['user_id'] = $data['user_id'];
            $order['buy_num'] = $data['buy_num'];
            $order['activity_id'] = $data['activity_id'];

            //生成订单
            $order_info = $order;
            $order_info['amount'] = $data['amount'];
            $order_info['payment_type'] = $data['payment_type'];
            $order_info['order_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $order_info['order_status'] = config('constants.order_status_unpay');
            Order::create($order_info);

            // 预占库存
            $this->updateStockNum($data['activity_id'], $data['buy_num']);

            //生成订单记录数据
            OrderRecord::create($order);

            //更新预扣系统中Redis排队状态为待支付，此时前端可跳转到支付页面
            //key: skstatus:goodsid:userid  skstatus:stock:1515161:23
            Redis::set(config('constants.pre_redis_status').config('constants.pre_redis_product').$data['product_id'].':'.$data['user_id'],2);
            //提交事务
            DB::commit();

            print_r('消息消费成功');
        }catch(Exception $e) {
            DB::rollBack();
            // 消费异常，则该任务将在 5 秒后被释放，并将继续重试最多 $tries = 3 次
            $this->release(5);
            print_r('消息消费失败'.$e->getMessage());
            Log::info('消息消费失败: '.$e->getMessage());
            //TODO 记录所有消费失败的订单入库
        }

    }

    /**
     * 更新库存
     * @throws InternalException
     */
    public function updateStockNum($activity_id, $buy_num): void
    {
        $activity = Activity::find($activity_id);
        if (!$activity) {
            throw new InternalException('库存数据丢失！');
        }

        $res = $activity->updateStockNum($buy_num);
        if (!$res) {
            //TODO 异步补偿处理：针对库存更新失败的情况，设计一个补偿机制，将更新失败的任务放入消息队列中，由后台任务异步进行补偿处理。
            throw new InternalException('更新库存失败！');
        }
    }
}

