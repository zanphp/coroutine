<?php

use ZanPHP\Coroutine\CallCC;
use ZanPHP\Coroutine\Contract\Resource;
use ZanPHP\Coroutine\FutureTask;
use ZanPHP\Coroutine\Parallel;
use ZanPHP\Coroutine\Signal;
use ZanPHP\Coroutine\SysCall;
use ZanPHP\Coroutine\Task;
use ZanPHP\Timer\Timer;

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

function getContextArray()
{
    return new SysCall(function (Task $task) {
        $context = $task->getContextArray();
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

function callcc(callable $fun)
{
    return new CallCC($fun);
}

function future($gen)
{
    if (is_callable($gen)) {
        $gen = $gen();
    }

    if (!$gen instanceof \Generator) {
        return null;
    }

    return new SysCall(function (Task $task) use($gen) {
        $ctx = $task->getContext();
        $future = new FutureTask($gen, $ctx, $task);
        $task->send($future);
        return Signal::TASK_CONTINUE;
    });
}


if (! function_exists('sys_echo')) {
    function sys_echo($context) {
        $workerId = isset($_SERVER["WORKER_ID"]) ? $_SERVER["WORKER_ID"] : "";
        $dataStr = date("Y-m-d H:i:s");
        echo "[$dataStr #$workerId] $context\n";
    }
}

if (! function_exists('sys_error')) {
    function sys_error($context) {
        $workerId = isset($_SERVER["WORKER_ID"]) ? $_SERVER["WORKER_ID"] : "";
        $dataStr = date("Y-m-d H:i:s");
        $context = str_replace("%", "%%", $context);
        fprintf(STDERR, "[$dataStr #$workerId] $context\n");
    }
}

if (! function_exists('echo_exception')) {
    /**
     * @param \Throwable $t
     */
    function echo_exception($t)
    {
        // 兼容PHP7 & PHP5
        if ($t instanceof \Throwable || $t instanceof \Exception) {
            $time = date('Y-m-d H:i:s');
            $class = get_class($t);
            $code = $t->getCode();
            $msg = $t->getMessage();
            $trace = $t->getTraceAsString();
            $line = $t->getLine();
            $file = $t->getFile();
            $metaData = "[]";
            if ($t instanceof \ZanPHP\Exception\ZanException) {
                $metaData = var_export($t->getMetadata(), true);
            }
            $workerId = isset($_SERVER["WORKER_ID"]) ? $_SERVER["WORKER_ID"] : -1;
            echo <<<EOF
        
        
###################################################################################
          \033[1;31mGot an exception\033[0m
          worker: #$workerId
          time: $time
          class: $class
          code: $code
          message: $msg
          file: $file::$line
metaData:
$metaData
          
$trace
###################################################################################


EOF;

            if ($previous = $t->getPrevious()) {
                echo "caused by:\n";
                echo_exception($previous);
            }
        }
    }
}