<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\SysCall;

use ZanPHP\Coroutine\Tests\Task\Job;

class KillTask extends Job{
    public function run() {
        $return = (yield 'SysCall.KillTask.calling');

        $this->context->set('step1', 'before task killed');

        yield killTask();

        $this->context->set('step2', 'after task killed');

        yield 'SysCall.KillTask';
    }
}