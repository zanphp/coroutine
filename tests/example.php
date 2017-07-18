<?php


use ZanPHP\Coroutine\Task;
use ZanPHP\Timer\Timer;
use ZanPHP\Timer\TimerExistException;


require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../../exception/vendor/autoload.php";



$task = function() {
    yield;
};
Task::execute($task());

Timer::after(1, function () {
    echo "~";
    swoole_event_exit();
}, 'a');


try {
    Timer::after(1, function () {

    }, 'a');
} catch (TimerExistException $e) {
    echo $e, "\n";
}
