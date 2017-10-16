<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests;

use ZanPHP\Coroutine\Task;
use ZanPHP\Testing\UnitTest;

class Base extends UnitTest{
    public function testTaskWork()
    {
        $coroutine = $this->step();
        $task = new Task($coroutine);
        $task->run();
    }
}