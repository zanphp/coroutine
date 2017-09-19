<?php

use ZanPHP\Coroutine\Task;

require __DIR__ . "/../vendor/autoload.php";


// 异常最终会被捕获，不会导致 Fatal Error
// 可注册 不注册，默认只打印

//[2017-08-23 02:58:47 #] Uncaught Exception: ex
//[2017-08-23 02:58:47 #] Uncaught ZanPHP\Exception\ZanException: async ex

Task::execute(function() {
    yield;
    throw new \Exception("ex");
});

Task::execute(function() {
    yield taskSleep(1);
    throw new \ZanPHP\Exception\ZanException("async ex");
});


call_user_func(function() {
    $task = Task::execute(function() {
        yield taskSleep(1);
        throw new \ZanPHP\Exception\ZanException("uncaught");
    });

    // 支持注册task异常回调
    $task->bindUncaughtExceptionEvent(function($ex) {
        echo "\n\n";
        echo "caught uncaught exception:\n";
        echo $ex, "\n";
    });
});



// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=


// Task 修改为 defer 执行
// 新的执行顺序为:
// >>>> A <<<<
// >>>> B <<<<
Task::execute(function() {
    yield;
    echo ">>>> B <<<<\n";
});
echo ">>>> A <<<<\n";