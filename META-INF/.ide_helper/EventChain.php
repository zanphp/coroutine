<?php
namespace Zan\Framework\Foundation\Coroutine;

class EventChain
{
    public function __construct(Event $event)
    {
    }

    /**
     * 连接N个传入的事件为事件链
     * @param args
     * @return bool
     */
    public function join()
    {
    }

    /**
     * 断开两个事件链接
     * @param $beforeEvt
     * @param $afterEvt
     */
    public function breakChain($beforeEvt, $afterEvt)
    {
    }

    public function after($beforeEvt, $afterEvt)
    {
    }

    public function before($beforeEvt, $afterEvt)
    {
    }

    public function fireEventChain($evtName)
    {
    }
}