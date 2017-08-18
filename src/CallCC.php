<?php

namespace ZanPHP\Coroutine;

use ZanPHP\Coroutine\Contract\Async;


class CallCC implements Async
{
    public $fun;

    public function __construct(callable $fun)
    {
        $this->fun = $fun;
    }

    public function execute(callable $cb, $task)
    {
        $fun = $this->fun;
        $fun($cb, $task);
    }
}
