<?php

namespace ZanPHP\Coroutine;

use ZanPHP\Coroutine\Contract\Async;
use ZanPHP\Coroutine\Exception\ConditionException;
use ZanPHP\Timer\Timer;

class Condition implements Async
{
    private $taskCallback = null;
    private $evtName;
    private $timerSet = false;
    private $waitN;

    public function __construct($evtName, $timeout = null, $waitN = 1)
    {
        if (is_integer($timeout) && $timeout > 0) {
            $this->timerSet = true;
            Timer::after($timeout, [$this, "onTimeout"], $this->getConditionTimeoutJobId());
        }
        StaticEvent::bind($evtName, [$this, "onEvent"]);
        $this->evtName = $evtName;
        $this->waitN = $waitN;
    }

    public function onEvent()
    {
        if ($this->timerSet) {
            Timer::clearAfterJob($this->getConditionTimeoutJobId());
        }
        if ($this->taskCallback) {
            if (--$this->waitN <= 0) {
                call_user_func($this->taskCallback, true);
                $this->taskCallback = null;
                StaticEvent::unregister($this->evtName);
            }
        }
    }

    public static function wakeUp($evtName)
    {
        StaticEvent::fire($evtName);
    }

    public function onTimeout()
    {
        StaticEvent::unbind($this->evtName, [$this, 'onEvent']);
        if ($this->taskCallback) {
            call_user_func($this->taskCallback, null, new ConditionException("condition {$this->evtName} timeout"));
            $this->taskCallback = null;
        }
    }

    public function execute(callable $callback, $task)
    {
        $this->taskCallback = $callback;
    }

    private function getConditionTimeoutJobId()
    {
        return spl_object_hash($this) . '_condition_timeout';
    }
}