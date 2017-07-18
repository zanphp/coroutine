<?php

namespace Zan\Framework\Foundation\Coroutine;


class Signal
{
    const TASK_SLEEP        = 1;
    const TASK_AWAKE        = 2;
    const TASK_CONTINUE     = 3;
    const TASK_KILLED       = 4;
    const TASK_RUNNING      = 5;
    const TASK_WAIT         = 6;
    const TASK_DONE         = 7;

    public static function isSignal($signal) {

    }
}