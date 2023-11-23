<?php

namespace App\Http\Controllers;

use App\Http\Requests\order\SubmitRequest;


use App\Services\OrderService;
use Carbon\Carbon;
use Exception;


class OrderController extends Controller
{

    public function submitData(OrderService $orderService, SubmitRequest $request): \Illuminate\Http\JsonResponse
    {
        //TODo 判断是否被限流
        try{
            //生成订单
            $orderService->submitOrder($request->all());
            return $this->success();
        }catch (Exception $ex) {
            //TODO 记录异常和入参
            return $this->error(['msg' => $ex->getMessage(), 'data' => []]);
        }
    }


}
