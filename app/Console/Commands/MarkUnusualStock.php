<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use phpseclib3\Math\BigInteger\Engines\PHP;

class MarkUnusualStock extends Command
{
    protected $signature = 'check:unusual_stock';

    protected $description = '检查mysql库存大于0,但redis库存等于0的异常数据，并进行标记';

    public function handle()
    {
        $this->info(Carbon::now()->format('Y-m-d H:i:s').': 开始执行库存检查标记逻辑');
        // 获取所有库存大于0的商品
        $my_stock = DB::table('t_activity')
            ->select("product_id")
            ->where('stock_num', '>', 0)
            ->where("status", 1)->get();
        if (empty($my_stock)){
            $this->info('没有异常库存数据');
            return;
        }
        $this->info('异常库存数据:');
        foreach ($my_stock as $item) {
            // 检查 Redis 中是否已经存在该商品库存
            $redisStock = Redis::get(config('constants.pre_redis_product') . $item->product_id);
            if ($redisStock == 0) {
                // 如果 Redis 中库存为0，则标记此商品的异常信息,即放到有序集合中，分数为当前时间戳
                print_r($item->product_id.PHP_EOL);
                Redis::zadd(config('constants.redis_stock_unusual_key'), 'NX', Carbon::now()->timestamp, $item->product_id);
            }
        }
        $this->info(Carbon::now()->format('Y-m-d H:i:s').': 库存检查标记执行完成');

    }
}
