<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */

namespace ZanPHP\Coroutine\Tests\Task;

use ZanPHP\Exception\System\InvalidArgumentException;
use ZanPHP\Coroutine\Tests\Context;

abstract class Job {
    protected $context = null;

    public function __construct(Context $context) {
        if(!$context) {
            throw new InvalidArgumentException('invlid context for Job __construct');
        }
        $this->context = $context;
    }

    abstract public function run();
}