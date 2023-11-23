<?php

namespace App\Http\Controllers;

use App\Http\Requests\reservation\AddUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\reservation\AddRequest;
use App\Models\ReserveInfo;
use App\Models\ReserveUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class ReserveController extends Controller
{
    const MaxCapacity = 10; //预约人数上限
    public function create(AddRequest $request)
    {
        // 创建预约活动
        $reserveInfo = ReserveInfo::create($request->all());
        //缓存预约结束时间
        Redis::set('reserve_end_time:'.$reserveInfo->id,Carbon::parse($reserveInfo->reserve_end_time)->timestamp);
        return $this->success(
            [
                'msg' => '预约活动创建成功',
                'data' => $reserveInfo
            ]
        );
    }

    /**
     * 添加预约资格
     * @param AddUserRequest $request
     * @param $reserveInfoId 预约活动id
     * @param ReserveUser $reserveUser
     * @return JsonResponse
     */
    public function addUser(AddUserRequest $request, $reserveInfoId, ReserveUser $reserveUser)
    {
        //进行预约熔断处理，预约人数超过指定上限，更改活动状态为：等待秒杀
        // 获取当前已预约人数和预约期结束时间
        $reservedCount = Redis::get('reserved_count:'.$reserveInfoId);
        $reserveEndTime = Redis::get('reserve_end_time:'.$reserveInfoId);
        // 如果预约期已结束，或已达到预约人数上限，返回预约失败的响应
        if (($reserveEndTime && time() > $reserveEndTime) || ($reservedCount && $reservedCount >= self::MaxCapacity)) {
            return $this->error(['msg' => '当前无法预约！', 'data' => []]);
        }
        // 查找对应的预约活动
        $reserveInfo = ReserveInfo::find($reserveInfoId);

        if (empty($reserveInfo)) {
            return $this->error(['msg' => '用户预约关系创建失败', 'data' => []]);
        }

        // 创建用户预约关系
        $reserveUser->reserveInfo()->associate($reserveInfo);
        $request->merge(
            [
                'reserve_time' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        );
        $reserveUser->fill($request->all());
        $reserveUser->save();

        //预约成功后，把数据缓存起来
        $params = $request->collect();
        $redis_key = "reserve_user_".$params->get("user_id");
        $redis_field = $params->get("sku_id");
        $value = json_encode($reserveInfo);
        Redis::hset($redis_key, $redis_field, $value);

        // 更新已预约人数和预约期结束时间
        $reservePeriod = Carbon::parse($reserveInfo->reserve_end_time)->timestamp;
        Redis::incr('reserved_count:'.$reserveInfoId);
        Redis::expire('reserved_count:'.$reserveInfoId, $reservePeriod);

        return $this->success(
            [
                'msg' => '成功添加预约资格',
                'data' => [
                    'reserve_id' =>  $reserveUser->id,
                ],
            ]
        );
    }

    public function cancelUser($reserveId, ReserveUser $reserveUser)
    {
        // 验证 $reserveId 是否是数字
        if(!is_numeric($reserveId)) {
            return $this->error([
                'msg' => '参数有误！',
                'data' => [],
            ]);
        }
        // 删除预约关系
        ReserveUser::destroy($reserveId);

        return $this->success(
            [
                'msg' => '成功取消预约资格',
                'data' => [],
            ]
        );
    }

    public function show(): array
    {
        return $this->userResponse(auth()->getToken()->get());
    }

    public function store(StoreRequest $request): array
    {
        $user = $this->user->create($request->validated()['user']);

        auth()->login($user);

        return $this->userResponse(auth()->refresh());
    }

    public function update(UpdateRequest $request): array
    {
        auth()->user()->update($request->validated()['user']);

        return $this->userResponse(auth()->getToken()->get());
    }

    public function login(LoginRequest $request): array
    {
        if ($token = auth()->attempt($request->validated()['user'])) {
            return $this->userResource($token);
        }

        abort(Response::HTTP_FORBIDDEN);
    }

    protected function userResponse(string $jwtToken): array
    {
        return ['user' => ['token' => $jwtToken] + auth()->user()->toArray()];
    }
}
