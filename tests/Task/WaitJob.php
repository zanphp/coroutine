<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\Task;


class WaitJob {
    public function run() {
        $data = (yield parallel([
            'a' => $this->asyncSeviceCall1(),
            'b' => $this->asyncSeviceCall2(),
        ]));

        $a = $data['a'];
        $b = $data['b'];
    }

    public function asyncSeviceCall1() {
        yield;
    }

    public function asyncSeviceCall2() {
        yield;
    }
}



function parallel($callMaps) {
    yield;
}