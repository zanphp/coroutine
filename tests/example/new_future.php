<?php

use ZanPHP\Coroutine\Task;

require __DIR__ . "/../../vendor/autoload.php";



// 以下同步异步只是语义上的含义, 本质上都是异步非阻塞的

// 正常获取结果
Task::execute(function() {
    // "异步"执行
    /** @var \ZanPHP\Coroutine\FutureTask $future */
    $future = (yield future(function() {
        yield taskSleep(10000);
        yield 42;
    }));

    // ...
    // do something else
    yield taskSleep(10);
    // ...

    // "阻塞"等待结果
    $r = (yield $future->get());
    var_dump($r);
});
//
//// 延迟捕获异常
//Task::execute(function() {
//    /** @var \ZanPHP\Coroutine\FutureTask $future */
//    $future = (yield future(function() {
//        yield taskSleep(1);
//        echo "throw\n";
//        throw new \Exception("Business Exception");
//    }));
//
//    yield taskSleep(200);
//
//    // 注意打印结果, 先抛出异常，然后调用get时异常才会被抛出
//    // 如果不调用get, 异常会被吃掉...
//    try {
//        $r = (yield $future->get());
//        var_dump($r);
//    } catch (\Throwable $t) {
//        echo "caught" . $t->getMessage(), "\n";
//    }
//});
//
//
//
//// 超时抛出异常
//Task::execute(function() {
//    /** @var \ZanPHP\Coroutine\FutureTask $future */
//    $future = (yield future(function() {
//        yield taskSleep(100);
//        yield 42;
//
//    }));
//
//
//    // 获取结果支持超时, 超时抛异常
//    try {
//        $r = (yield $future->get(10));
//        var_dump($r);
//    } catch (\Throwable $t) {
//        echo get_class($t), "\n";
//    }
//});
//
//
//// context
//Task::execute(function() {
//    yield setContext("id", 42);
//
//    /** @var \ZanPHP\Coroutine\FutureTask $future */
//    $future = (yield future(function() {
//        yield taskSleep(1);
//        echo "get id: " . (yield getContext("id")), "\n";
//        yield setContext("name", "future");
//    }));
//
//
//    try {
//        // future 内部set到ctx的值，是否能获取到看时序，未定义, get()到结果无异常时可以获取到的
//        yield $future->get();
//        echo "get name ", (yield getContext("name")), "\n";
//    } catch (\Throwable $t) {
//        echo get_class($t), "\n";
//    }
//});
//
//
//// 超时之后抛出异常
//// 打印出 [2017-08-23 02:53:15 #] Uncaught Exception: 超时之后抛异常
//Task::execute(function() {
//    yield setContext("id", 42);
//
//    /** @var \ZanPHP\Coroutine\FutureTask $future */
//    $future = (yield future(function() {
//        yield taskSleep(200);
//        throw new \Exception("超时之后抛异常");
//    }));
//
//
//    try {
//        yield $future->get(10);
//    } catch (\Throwable $t) {
//        echo get_class($t), "\n";
//    }
//});