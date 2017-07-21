<?php
namespace Zan\Framework\Foundation\Core;

class EventChain
{
    public static function clear()
    {

    }

    /**
     * 连接N个传入的事件为事件链
     * @param args
     * @return bool
     */
    public static function join()
    {

    }

    /**
     * 断开两个事件链接
     * @param $beforeEvt
     * @param $afterEvt
     */
    public static function breakChain($beforeEvt, $afterEvt)
    {

    }

    public static function after($beforeEvt, $afterEvt)
    {

    }

    public static function before($beforeEvt, $afterEvt)
    {

    }

    public static function fireEventChain($evtName)
    {

    }
}