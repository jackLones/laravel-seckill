<?php

namespace App\Services;

use App\Events\OrderShipped;
use App\Exceptions\InternalException;
use App\Jobs\GenerateOrder;
use Exception;
use Illuminate\Support\Facades\Redis;

class OrderService
{
    const PRE_PRODUCT_ID = "stock";

    const STORE_DEDUCTION_SCRIPT_LUA = '
                -- 判断这个用户是不是已经抢到过了
                -- key1: 用户id, key2: 商品id
                -- key是： sklimit:goodsid:userid
                local key = "sklimit:" .. KEYS[2] .. ":" .. KEYS[1]
                -- 抢购数量
                local quantity = tonumber(ARGV[1])
                
                -- key是： skstatus:goodsid:userid
                local keyStatus = "skstatus:" .. KEYS[2] .. ":" .. KEYS[1]
                
                -- 使用 SETNX 命令来判断是否已经抢购过
                if redis.call("SETNX", key, 1) == 1 then
                    return -1  -- 已经抢购过了
                end
                
                -- 调用 Redis 的 GET 命令，查询活动库存
                local stock = tonumber(redis.call("GET", KEYS[2]))
                
                -- 判断活动库存是否充足
                if not stock or stock < 1 then
                    return -2  -- 活动库存不足
                end
                
                -- 如果活动库存充足，则进行扣减操作
                redis.call("DECRBY", KEYS[2], quantity)
                -- 1是待付款
                redis.call("SET", keyStatus, 1)
                
                return 1  -- 抢购成功
            '; // LUA扣减库存脚本

    public static $store_deduction_script_sha1;
    public function __construct()
    {
        self::$store_deduction_script_sha1 = Redis::script('load',self::STORE_DEDUCTION_SCRIPT_LUA);
    }

    public static function evalsha($key1, $key2, $quantity)
    {
        // 使用 EvalSha 方法执行脚本
        $key_num = 2; // Lua 脚本所需要的键的数量
        return Redis::evalSha(self::$store_deduction_script_sha1, $key_num, $key1, $key2, $quantity);
    }

    /**
     * @throws InternalException
     * @throws Exception
     */
    public function submitOrder(array $params = []): void
    {
        // 限购
        $buy_num = $params['buy_num'];
        $product_id = $params['product_id'];
        $user_id = $params['user_id'];
        $count = self::evalSha($user_id, self::PRE_PRODUCT_ID.':'.$product_id, $buy_num);
        if ($count < 0) {
            throw new InternalException("param: {$count}, redis desc fail");
        }

        //异步生成订单信息等
        $info = [
            "activity_id" => $params['activity_id'],
            "product_id" => $product_id,
            'user_id' => $user_id,
            'buy_num' => $buy_num,
            'amount' => $params['amount'],
            'payment_type' => $params['payment_type'],
        ];
        GenerateOrder::dispatch($info);
    }

}


