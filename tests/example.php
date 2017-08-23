<?php

use ZanPHP\Coroutine\Task;

require __DIR__ . "/../vendor/autoload.php";


call_user_func(function() {
    $task = Task::execute(function() {
        yield taskSleep(100);
        yield 42;
    });

    $task->bindTaskDoneEvent(function($retVal) {
        assert($retVal === 42);
        echo "DONE\n";
    });
});


call_user_func(function() {
    $task = Task::execute(function() {
        yield taskSleep(100);
        yield 42;
    });

    $task->bindTaskDoneEvent(function($retVal, $ex) {
        assert($retVal === 42);
        // FIXBUG task done event 抛异常死循环
        throw new \Exception("XXXX");
    });
});

call_user_func(function() {
    $task = Task::execute(function() {
        yield taskSleep(1);
        throw new \Exception("uncaughtEx");
    });

    $task->bindUncaughtExceptionEvent(function(\Throwable $t) {
        assert($t->getMessage() === "uncaughtEx");
        echo "UncaughtException\n";
    });
});



call_user_func(function() {
    $task = Task::execute(function() {
        yield;
        throw new \Exception("ex2");
    });
    // 不绑定被忽略，防止fatal error
    $task->bindTaskDoneEvent(function($retVal) {});
});