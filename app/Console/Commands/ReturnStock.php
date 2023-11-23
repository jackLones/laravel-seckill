<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class ReturnStock extends Command
{
    protected $signature = 'return:stock';

    const TIME_INTERVAL = 300; //5分钟
    protected $description = '把异常库存加回到redis中';

    public function handle()
    {
        $this->info(Carbon::now()->format('Y-m-d H:i:s').': 开始执行返还库存逻辑');
        //处理mysql库存大于0,但redis库存等于0的商品且这种情况持续了5分钟，即：把库存加回到redis中
        $need_back_product_ids = []; //是需要回加的product_ids
        $unusual_data = Redis::zrevrange('stock_unusual_zero', 0, -1, 'WITHSCORES');
//        print_r($unusual_data);
        foreach ($unusual_data as $member => $score) {
            if (abs(Carbon::now()->timestamp - $score) > self::TIME_INTERVAL) {
                //持续了5分钟
                $need_back_product_ids[] = $member;
            }
        }

        // 获取所有库存大于0的商品
        $my_stock = DB::table('t_activity')
            ->select('product_id','stock_num')
            ->where('stock_num', '>', 0)
            ->where("status", 1)
            ->whereIn('product_id', $need_back_product_ids)
            ->get();
        if (empty($my_stock)) {
            $this->info('无需要返还的库存数据');
            return;
        }
        $newItems = collect($my_stock)->pluck('stock_num', 'product_id')->toArray();
        print_r($newItems);
        foreach ($newItems as $key => $stock) {
            if ($unusual_data[$key]) {
                // 返还库存
                print_r($stock.PHP_EOL);
                Redis::set(config('constants.pre_redis_product') . $key, $stock);
                Redis::zrem(config('constants.redis_stock_unusual_key'), $key);
                //删除对应的zset数据

            }
        }

        $this->info(Carbon::now()->format('Y-m-d H:i:s').': 返还库存逻辑执行完成');
    }
}
