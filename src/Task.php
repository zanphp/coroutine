<?php

namespace ZanPHP\Coroutine;


use ZanPHP\Timer\Timer;

class Task
{
    protected $taskId = 0;
    protected $parentTask;
    protected $coroutine = null;
    protected $context = null;

    protected $sendValue = null;
    protected $scheduler = null;
    protected $status = 0;

    public static function execute($coroutine, Context $context = null, $taskId = 0, Task $parentTask = null)
    {
        if (is_callable($coroutine)) {
            return static::execute($coroutine(), $context, $taskId, $parentTask);
        }

        if ($coroutine instanceof \Generator) {
            $task = new Task($coroutine, $context, $taskId, $parentTask);
            // 这里应该使用defer方式运行!!!, 这样才有机会先绑定task事件,才开始迭代, swoole_event_defer()有问题
            // master 不能使用定时器
            if (swoole_timer_after(1, function() use($task) { $task->run(); }) === false) {
                $task->run();
            }
            return $task;
        }

        return $coroutine;
    }

    public function __construct(\Generator $coroutine, Context $context = null, $taskId = 0, Task $parentTask = null)
    {
        $this->coroutine = $this->caughtCoroutine($coroutine);
        $this->taskId = $taskId ? $taskId : TaskId::create();
        $this->parentTask = $parentTask;

        if ($context) {
            $this->context = $context;
        } else {
            $this->context = new Context();
        }

        $this->scheduler = new Scheduler($this);
    }

    public function run()
    {
        while (true) {
            try {
                if ($this->status === Signal::TASK_KILLED) {
                    $this->fireTaskDoneEvent();
                    $this->status = Signal::TASK_DONE;
                    break;
                }
                $this->status = $this->scheduler->schedule();
                switch ($this->status) {
                    case Signal::TASK_KILLED:
                        return null;
                    case Signal::TASK_SLEEP:
                        return null;
                    case Signal::TASK_WAIT:
                        return null;
                    case Signal::TASK_DONE:
                        $this->fireTaskDoneEvent();
                        return null;
                    default:
                        continue;
                }
            } catch (\Throwable $t) {
                $this->scheduler->throwException($t);
            } catch (\Exception $e) {
                $this->scheduler->throwException($e);
            }
        }
    }

    public function sendException($e)
    {
        $this->scheduler->throwException($e);
    }

    public function send($value)
    {
        try {
            $this->sendValue = $value;
            return $this->coroutine->send($value);
        } catch (\Throwable $t) {
            $this->sendException($t);
        } catch (\Exception $e) {
            $this->sendException($e);
        }
    }

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getContextArray()
    {
        return $this->context->getAll();
    }

    public function getSendValue()
    {
        return $this->sendValue;
    }

    public function getResult()
    {
        return $this->sendValue;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($signal)
    {
        $this->status = $signal;
    }

    public function getCoroutine()
    {
        return $this->coroutine;
    }

    public function setCoroutine(\Generator $coroutine)
    {
        $this->coroutine = $coroutine;
    }

    public function getParentTask()
    {
        return $this->parentTask;
    }

    public function bindTaskDoneEvent(callable $callback)
    {
        $evtName = 'task_event_' . $this->taskId;
        $this->context->getEvent()->bind($evtName, $callback,Event::ONCE_EVENT);
    }

    public function fireTaskDoneEvent()
    {
        if (null === $this->context) {
            return;
        }
        $evtName = 'task_event_' . $this->taskId;

        // TaskDoneEvent 不能抛出异常, 否则死循环
        try {
            $this->context->getEvent()->fire($evtName, $this->sendValue);
            return;
        } catch (\Throwable $e) {
        } catch (\Exception $e) {}
        sys_echo("Uncaught " . get_class($e));
        echo_exception($e);
    }

    public function bindUncaughtExceptionEvent(callable $callback)
    {
        $evtName = 'task_event_ex_' . $this->taskId;
        $this->context->getEvent()->bind($evtName, $callback,Event::ONCE_EVENT);
    }

    // taskDone 与 exception 应该使用一个回调, 但是fireEvent的参数设计有问题!!!
    public function fireUncaughtExceptionEvent($e)
    {
        if (null === $this->context) {
            return;
        }

        $doneEvtName = 'task_event_' . $this->taskId;
        $this->context->getEvent()->unregister($doneEvtName);

        $evtName = 'task_event_ex_' . $this->taskId;

        try {
            $this->context->getEvent()->fire($evtName, $e);
            return;
        } catch (\Throwable $e) {
        } catch (\Exception $e) {}
        sys_echo("In fireUncaughtExceptionEvent, Uncaught " . get_class($e));
        echo_exception($e);
    }

    private function caughtCoroutine(\Generator $gen)
    {
        try {
            yield $gen;
            return;
        } catch (\Throwable $e) {
        } catch (\Exception $e) {}
        sys_echo("In caughtCoroutine, Uncaught " . get_class($e));
        echo_exception($e);
        $this->fireUncaughtExceptionEvent($e);
    }
}
