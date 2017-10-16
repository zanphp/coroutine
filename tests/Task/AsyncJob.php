<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\Task;


class AsyncJob extends Job {
    private $rpc = null;
    public function run() {
        $response = (yield $this->call());

        $this->context->set('response', $response);

        yield $response;
    }

    public function fakeResponse() {
        return $this->rpc->fakeResponse();
    }

    private function call() {
        $this->context->set('call()','call');
        $this->rpc = new AsyncTest();

        yield $this->rpc;
    }
}