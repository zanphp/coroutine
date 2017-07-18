<?php

namespace  Zan\Framework\Foundation\Coroutine;

use Zan\Framework\Network\Server\Timer\Timer;
use Zan\Framework\Network\Server\Timer\TimerExistException;

require __DIR__ . "/../../exception/vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";



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
