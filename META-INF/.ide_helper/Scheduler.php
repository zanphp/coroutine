<?php

namespace Zan\Framework\Foundation\Coroutine;

use Zan\Framework\Foundation\Contract\Async;

class Scheduler
{
    public function __construct(Task $task)
    {
    }

    public function schedule()
    {
    }

    public function isStackEmpty()
    {
    }

    public function throwException($e, $isFirstCall = false, $isAsync = false)
    {
    }

    public function asyncCallback($response, $exception = null)
    {
    }
}
