<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom logs levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // 自定义处理 ValidationException 类型的异常
        if ($exception instanceof ValidationException) {
            return response()->json([
                'code' => 201,
                'status' => 'error',
                'data' => [
                    'message' => $exception->errors(),
                ],
            ], 422);
        }

        // 自定义处理 InternalException 类型的异常
//        if ($exception instanceof InternalException) {
//            // 执行自定义的异常渲染逻辑
//            // 例如，返回一个自定义的错误页面
//            return response()->json([
//                'code' => 201,
//                'msg' => '操作失败',
//            ], 400);
//        }

        if ($exception instanceof QueryException) {
            // 返回自定义的错误响应
            return response()->json([
                'code' => 201,
                'msg' => '操作失败',
            ], 400);
        }

        // 自定义处理 Exception 类型的异常
        if ($exception instanceof Exception) {
            // 返回自定义的错误响应
            return response()->json([
                'code' => 201,
                'msg' => '操作失败！',
            ], 400);
        }

        // 其他类型的异常，使用默认处理方式
        return parent::render($request, $exception);
    }
}
