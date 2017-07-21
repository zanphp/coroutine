<?php

namespace ZanPHP\Coroutine;

class StaticEvent
{
    private static $evtMap = [];

    const NORMAL_EVENT = 1;
    const ONCE_EVENT = 2;

    public static function clear()
    {
        self::$evtMap = [];
        StaticEventChain::clear();
    }

    public static function register($evtName)
    {
        if (!isset(self::$evtMap[$evtName])) {
            self::$evtMap[$evtName] = [];
        }
    }

    public static function unregister($evtName)
    {
        if (isset(self::$evtMap[$evtName])) {
            unset(self::$evtMap[$evtName]);
        }
    }

    public static function once($evtName, $callback)
    {
        self::register($evtName);

        self::$evtMap[$evtName][] = [
            'callback' => $callback,
            'evtType' => static::ONCE_EVENT,
        ];
    }

    public static function bind($evtName, $callback)
    {
        self::register($evtName);

        self::$evtMap[$evtName][] = [
            'callback' => $callback,
            'evtType' => static::NORMAL_EVENT,
        ];
    }

    public static function unbind($evtName, $callback)
    {
        if (!isset(self::$evtMap[$evtName]) || !self::$evtMap[$evtName]) {
            return false;
        }

        foreach (self::$evtMap[$evtName] as $key => $evt) {
            $cb = $evt['callback'];
            if ($cb == $callback) {
                unset(self::$evtMap[$evtName][$key]);
                return true;
            }
        }
        return false;
    }

    public static function fire($evtName, $args=null, $loop=true)
    {
        if (isset(self::$evtMap[$evtName]) && self::$evtMap[$evtName]) {
            self::fireEvents($evtName, $args, $loop);
        }

        StaticEventChain::fireEventChain($evtName);
    }

    private static function fireEvents($evtName, $args=null, $loop=true)
    {
        foreach (self::$evtMap[$evtName] as $key => $evt) {
            $callback = $evt['callback'];
            $evtType = $evt['evtType'];

            if (static::ONCE_EVENT === $evtType) {
                unset(self::$evtMap[$evtName][$key]);
            }
            call_user_func($callback, $args);

            if(false === $loop){
                break;
            }
        }
    }
}
