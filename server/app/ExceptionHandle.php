<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        
        // 处理验证异常
        if ($e instanceof ValidateException) {
            return json([
                'code' => 0,
                'msg' => $e->getError(),
                'data' => []
            ], 200);
        }
        
        // 处理数据未找到异常
        if ($e instanceof DataNotFoundException || $e instanceof ModelNotFoundException) {
            return json([
                'code' => 0,
                'msg' => '数据不存在',
                'data' => []
            ], 200);
        }
        
        // 处理HTTP异常
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage();
            
            // 404错误
            if ($statusCode == 404) {
                return json([
                    'code' => 0,
                    'msg' => '请求的资源不存在',
                    'data' => []
                ], 404);
            }
            
            // 500错误
            if ($statusCode == 500) {
                return json([
                    'code' => 0,
                    'msg' => '服务器内部错误',
                    'data' => []
                ], 500);
            }
            
            return json([
                'code' => 0,
                'msg' => $message,
                'data' => []
            ], $statusCode);
        }
        
        // 生产环境隐藏详细错误信息
        if (!env('app_debug', false)) {
            return json([
                'code' => 0,
                'msg' => '系统繁忙，请稍后再试',
                'data' => []
            ], 500);
        }

        // 其他错误交给系统处理（仅在调试模式）
        return parent::render($request, $e);
    }
}
