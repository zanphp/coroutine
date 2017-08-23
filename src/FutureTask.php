<?php

namespace ZanPHP\Coroutine;


use ZanPHP\Coroutine\Contract\Async;
use ZanPHP\Coroutine\Exception\AsyncTimeoutException;
use ZanPHP\Timer\Timer;

final class FutureTask
{
    const PENDING = 1;
    const DONE = 2;
    const TIMEOUT = 3;

    private $task;
    private $timerId;
    private $cc;

    public $state;
    public $result;
    public $ex;

    public function __construct(\Generator $gen, Context $ctx, Task $parent = null)
    {
        $this->state = self::PENDING;

        $this->task = new Task($gen, $ctx, 0, $parent);

        $this->task->bindTaskDoneEvent(function($r) {
            if ($this->state === self::TIMEOUT) {
                return;
            }
            $this->state = self::DONE;

            if ($cc = $this->cc) {
                if ($this->timerId) {
                    Timer::clearAfterJob($this->timerId);
                }
                $cc($r, null);
            } else {
                $this->result = $r;
            }
        });

        $this->task->bindUncaughtExceptionEvent(function($ex) {
            if ($this->state === self::TIMEOUT) {
                return;
            }
            $this->state = self::DONE;

            if ($cc = $this->cc) {
                if ($this->timerId) {
                    swoole_timer_clear($this->timerId);
                }
                $cc(null, $ex);
            } else {
                $this->ex = $ex;
            }
        });

        $this->task->run();
    }

    /**
     * 获取异步任务结果, 当设置超时时间, 超时之后会抛出异常
     * @param int $timeout 0 means block forever
     * @return Async
     * @throws AsyncTimeoutException
     */
    public function get($timeout = 0)
    {
        return callcc(function($cc) use($timeout) {
            if ($this->state === self::DONE) {
                $cc($this->result, $this->ex);
            } else {
                $this->cc = $cc;
                $this->getResultTimeout($timeout);
            }
        });
    }

    private function getResultTimeout($timeout)
    {
        if (!$timeout) {
            return;
        }

        $this->timerId = Timer::after($timeout, function() {
            assert($this->state === self::PENDING);
            $this->task->setStatus(Signal::TASK_KILLED);
            $this->state = self::TIMEOUT;
            $cc = $this->cc;
            $cc(null, new AsyncTimeoutException());
        });
    }
}