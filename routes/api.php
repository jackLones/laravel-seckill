<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReserveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
use App\Models\ReserveInfo;
Route::prefix('reserve')->group(function () {
    Route::post('/', [ReserveController::class, 'create']);
    Route::post('/{reserveInfoId}/users', [ReserveController::class, 'addUser']); // 添加预约资格
    Route::delete('/{reserveId}/users', [ReserveController::class, 'cancelUser']); // 取消预约资格
});

Route::prefix('order')->group(function () {
    Route::post('/submitData', [OrderController::class, 'submitData']); // 提交订单
});


