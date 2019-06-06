<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class Num
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key='num'.$_SERVER['REMOTE_ADDR'];
        $num=Redis::get($key);
        if($num>20){
            //防恶意请求
            Redis::expire($key,180);
            die('超过请求限制次数');
        }
        Redis::incr($key);
        Redis::expire($key,60);
        return $next($request);
    }
}
