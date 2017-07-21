<?php
namespace Zan\Framework\Foundation\Core;

class Event
{
    const NORMAL_EVENT = 1;
    const ONCE_EVENT = 2;

    public static function clear()
    {

    }

    public static function register($evtName)
    {

    }

    public static function unregister($evtName)
    {

    }

    public static function once($evtName, $callback)
    {

    }

    public static function bind($evtName, $callback)
    {

    }

    public static function unbind($evtName, $callback)
    {

    }

    public static function fire($evtName, $args=null, $loop=true)
    {
        if (isset(self::$evtMap[$evtName]) && self::$evtMap[$evtName]) {
            self::fireEvents($evtName, $args, $loop);
        }

        EventChain::fireEventChain($evtName);
    }
}
