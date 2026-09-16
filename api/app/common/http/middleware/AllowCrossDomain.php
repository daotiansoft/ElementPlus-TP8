<?php


namespace app\common\http\middleware;

use Closure;

/**
 * 自定义跨域中间件
 */
class AllowCrossDomain
{
    /**
     * 跨域处理
     *
     * @author zero
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        header('Access-Control-Max-Age: 1728000');
        header('Access-Control-Allow-Credentials:true');
        if (strtoupper($request->method()) == 'OPTIONS') {
            return response();
        }
        return $next($request);
    }
}