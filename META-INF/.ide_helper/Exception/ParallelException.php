<?php

namespace Zan\Framework\Foundation\Exception;


class ParallelException extends ZanException
{
    public function __construct($message, $code, \Exception $previous, array $metaData, $parallelResult = null)
    {
    }

    public static function makeWithResult($result, array $exceptions)
    {
    }

    public function __toString()
    {
    }

    public function getParallelResult()
    {
    }

    public function getExceptions()
    {
    }
}