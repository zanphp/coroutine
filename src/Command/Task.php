<?php

use Zan\Framework\Foundation\Contract\Resource;
use Zan\Framework\Foundation\Coroutine\Parallel;
use Zan\Framework\Foundation\Coroutine\Signal;
use Zan\Framework\Foundation\Coroutine\SysCall;
use Zan\Framework\Foundation\Coroutine\Task;
use Zan\Framework\Network\Server\Timer\Timer;

function taskSleep($ms)
{
    return new SysCall(function (Task $task) use ($ms) {
        Timer::after($ms, function () use ($task) {
            $task->send(null);
            $task->run();
        });

        return Signal::TASK_SLEEP;
    });
}

function newTask(\Generator $gen = null)
{
    return new SysCall(function (Task $task) use ($gen) {
        $context = $task->getContext();
        Task::execute($gen, $context, 0, $task);

        $task->send(null);
        return Signal::TASK_CONTINUE;
    });
}

function go(\Generator $coroutine)
{
    return newTask($coroutine);
}

function defer(callable $callback)
{

}

function deferRelease(Resource $res, $stradegy = Resource::AUTO_RELEASE)
{
}

function release(Resource $res, $stradegy = Resource::AUTO_RELEASE)
{

}

function killTask()
{
    return new SysCall(function (Task $task) {
        return Signal::TASK_KILLED;
    });
}

function getTaskId()
{
    return new SysCall(function (Task $task) {
        $task->send($task->getTaskId());

        return Signal::TASK_CONTINUE;
    });
}



function getContext($key, $default = null)
{
    return new SysCall(function (Task $task) use ($key, $default) {
        $context = $task->getContext();
        $task->send($context->get($key, $default));

        return Signal::TASK_CONTINUE;
    });
}

function setContext($key, $value)
{
    return new SysCall(function (Task $task) use ($key, $value) {
        $context = $task->getContext();
        $task->send($context->set($key, $value));

        return Signal::TASK_CONTINUE;
    });
}

function getContextObject()
{
    return new SysCall(function (Task $task) {
        $context = $task->getContext();
        $task->send($context);

        return Signal::TASK_CONTINUE;
    });
}

function getTaskResult()
{
    return new SysCall(function (Task $task) {
        $task->send($task->getSendValue());

        return Signal::TASK_CONTINUE;
    });
}

function getTaskStartTime($format = null)
{
    return new SysCall(function (Task $task) use ($format) {
    });
}

function waitFor(\Generator $coroutine)
{
    return new SysCall(function (Task $task) use ($coroutine) {

    });
}

function wait()
{
    return new SysCall(function (Task $task) {

    });
}

function parallel($coroutines, &$fetchCtx = [])
{
    return new SysCall(function (Task $task) use ($coroutines, &$fetchCtx) {
        (new Parallel($task))->call($coroutines, $fetchCtx);

        return Signal::TASK_WAIT;
    });
}

function async(callable $callback)
{
    return new SysCall(function (Task $task) use ($callback) {
        $context = $task->getContext();
        $queue = $context->get('async_task_queue', []);
        $queue[] = $callback;
        $context->set('async_task_queue', $queue);
        $task->send(null);

        return Signal::TASK_CONTINUE;
    });
}