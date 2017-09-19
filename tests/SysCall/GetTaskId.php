<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\SysCall;

use ZanPHP\Coroutine\Tests\Task\Job;

class GetTaskId extends Job
{
    public function run()
    {
        $value = (yield getTaskId());

        $this->context->set('taskId', $value);

        yield 'SysCall.GetTastId';
    }
}