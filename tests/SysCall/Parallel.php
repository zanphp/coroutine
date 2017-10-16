<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\SysCall;

use ZanPHP\Coroutine\Tests\Task\Job;

class Parallel extends Job
{
    public function run()
    {
        $firstValue = $this->context->get('first_coroutine');
        $secondValue = $this->context->get('second_coroutine');
        $thirdValue = $this->context->get('third_coroutine');

        $coroutines = [
            $this->firstCoroutine($firstValue),
            $this->secondCoroutine($secondValue),
            $this->getFunctionResult($thirdValue),
            $this->sysCall()
        ];

        $value = (yield parallel($coroutines));
        $this->context->set('parallel_value', $value);
        yield 'SysCall.Parallel';
    }


    private function firstCoroutine($value)
    {
        yield $value;
    }

    private function secondCoroutine($value)
    {
        yield $value;
    }

    private function getFunctionResult($thirdValue)
    {
        return $thirdValue;
    }

    private function sysCall()
    {
        yield getTaskId();
    }
}