<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\Task;

use ZanPHP\Coroutine\Contract\Async;

class AsyncTest implements  Async
{
    private $callback = null;

    public function execute(callable $callback, $task)
    {
        $this->callback = $callback;
    }

    public function fakeResponse() {
        $response = new Response(200,'ok',['data'=>'rpc']);
        call_user_func($this->callback, $response);
    }
}