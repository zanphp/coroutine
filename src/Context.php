<?php

namespace ZanPHP\Coroutine;


class Context
{
    private $map = [];
    private $event = null;

    public function __construct()
    {
        $this->map = [];
        $this->event = new Event();
    }

    public function get($key, $default = null, $class = null)
    {
        if (!isset($this->map[$key])) {
            return $default;
        }

        if (null === $class) {
            return $this->map[$key];
        }

        if ($this->map[$key] instanceof $class
            || is_subclass_of($this->map[$key], $class)
        ) {
            return $this->map[$key];
        }

        return $default;
    }

    public function set($key, $value)
    {
        $this->map[$key] = $value;
    }

    public function getAll()
    {
        return $this->map;
    }

    public function merge($ctx, $override = true)
    {
        if ($ctx instanceof static) {
            $ctx = $ctx->map;
        }

        if (is_array($ctx) && $ctx) {
            if ($override) {
                $this->map = self::arrayMergeRecursively($this->map, $ctx);
            } else {
                $this->map = self::arrayMergeRecursively($ctx, $this->map);
            }
        }
    }

    public function clear()
    {
        foreach ($this->map as $value) {
            unset($value);
        }
        unset($this->map);
        $this->map = null;
        $this->event = null;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getEventChain()
    {
        return $this->event->getEventChain();
    }

    private static function arrayMergeRecursively()
    {
        $total = func_num_args();
        if(0 === $total){
            return [];
        }

        $result = func_get_arg(0);
        for ($i = 1; $i < $total; $i++) {
            foreach (func_get_arg($i) as $key => $val) {
                if (!isset($result[$key])) {
                    $result[$key] = $val;
                    continue;
                }

                if (is_array($val) && is_array($result[$key])) {
                    $result[$key] = self::arrayMergeRecursively($result[$key], $val);
                } else {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }
}