<?php
namespace Zan\Framework\Foundation\Coroutine;

class Event
{
    const NORMAL_EVENT = 1;
    const ONCE_EVENT = 2;

    public function __construct()
    {
    }

    public function getEventChain()
    {
    }

    public function register($evtName)
    {
    }

    public function unregister($evtName)
    {
    }

    public function once($evtName, callable $callback)
    {
    }

    public function bind($evtName, callable $callback, $evtType=Event::NORMAL_EVENT)
    {
    }

    public function unbind($evtName, callable $callback)
    {
    }

    public function fire($evtName, $args=null, $loop=true)
    {
    }
}
