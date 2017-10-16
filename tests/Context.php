<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests;


class Context {
    private $map = [];

    public function __construct() {
        $this->map = [];
    }

    public function get($key, $default=null) {
        if( isset($this->map[$key]) ) {
            return $this->map[$key];
        }

        return $default;
    }

    public function set($key, $value) {
        $this->map[$key] = $value;
    }

    public function show() {
        return $this->map;
    }
}