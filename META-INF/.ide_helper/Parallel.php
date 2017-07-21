<?php

namespace Zan\Framework\Foundation\Coroutine;

use Zan\Framework\Foundation\Exception\ParallelException;
use Zan\Framework\Foundation\Exception\System\InvalidArgumentException;
use Zan\Framework\Utilities\DesignPattern\Context;

class Parallel
{
    public function __construct(Task $task)
    {
    }

    public function call($coroutines, &$fetchCtx = [])
    {
    }

    public function done()
    {
    }
}
