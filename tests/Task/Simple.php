<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\Task;


class Simple extends Job {
    public function run() {
        $value = (yield 'simple value');
        $this->context->set('key', $value);

        yield 'simple job done';
    }
}