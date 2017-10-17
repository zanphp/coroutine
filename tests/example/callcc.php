<?php

use ZanPHP\Coroutine\Task;

require __DIR__ . "/../vendor/autoload.php";


Task::execute(function() {
    $ip = (yield callcc(function($cc) {
        $r = swoole_async_dns_lookup("www.youzan.com", function($_, $ip) use($cc) {
            $cc($ip);
        });
        if (!$r) {
            $cc(null);
        }
    }));

    var_dump($ip);
});