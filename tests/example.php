<?php


use ZanPHP\Coroutine\Task;


require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../../exception/vendor/autoload.php";



Task::execute(function() {
    yield taskSleep(1000);
    echo "done";
});