<?php
namespace Zan\Framework\Foundation\Coroutine;

class SysCall
{
    protected $callback = null;

    public function __construct(\Closure $callback)
    {
    }

    public function __invoke(Task $task)
    {
    }
}