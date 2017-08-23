<?php

use ZanPHP\Coroutine\Task;

require __DIR__ . "/../vendor/autoload.php";


// 以下同步异步只是语义上的含义, 本质上都是异步非阻塞的

// 正常获取结果
Task::execute(function() {
    // "异步"执行
    /** @var \ZanPHP\Coroutine\FutureTask $future */
    $future = yield future(function() {
        yield taskSleep(100);
        yield 42;
    });

    // ...
    // do something else
    yield taskSleep(10);
    // ...

    // "阻塞"等待结果
    $r = yield $future->get();
    var_dump($r);
});

// 延迟捕获异常
Task::execute(function() {
    /** @var \ZanPHP\Coroutine\FutureTask $future */
    $future = yield future(function() {
        yield taskSleep(1);
        echo "throw\n";
        throw new \Exception("Business Exception");
    });

    yield taskSleep(200);

    // 注意打印结果, 先抛出异常，然后调用get时异常才会被抛出
    // 如果不调用get, 异常会被吃掉...
    try {
        $r = yield $future->get();
        var_dump($r);
    } catch (\Throwable $t) {
        echo "caught" . $t->getMessage(), "\n";
    }
});



// 超时抛出异常
Task::execute(function() {
    /** @var \ZanPHP\Coroutine\FutureTask $future */
    $future = yield future(function() {
        yield taskSleep(100);
        yield 42;

    });


    // 获取结果支持超时, 超时抛异常
    try {
        $r = yield $future->get(10);
        var_dump($r);
    } catch (\Throwable $t) {
        echo get_class($t), "\n";
    }
});
