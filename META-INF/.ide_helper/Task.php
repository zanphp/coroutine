<?php

namespace Zan\Framework\Foundation\Coroutine;

use Zan\Framework\Utilities\DesignPattern\Context;

class Task
{
    public static function execute($coroutine, Context $context = null, $taskId = 0, Task $parentTask = null)
    {
    }

    public function __construct(\Generator $coroutine, Context $context = null, $taskId = 0, Task $parentTask = null)
    {
    }

    public function run()
    {
    }

    public function sendException($e)
    {
    }

    public function send($value)
    {

    }

    public function getTaskId()
    {
    }

    public function getContext()
    {
    }

    public function getSendValue()
    {
    }

    public function getResult()
    {
    }

    public function getStatus()
    {
    }

    public function setStatus($signal)
    {
    }

    public function getCoroutine()
    {
    }

    public function setCoroutine(\Generator $coroutine)
    {
    }

    public function getParentTask()
    {
    }

    public function fireTaskDoneEvent()
    {
    }
}
