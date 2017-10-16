<?php
/**
 * Created by PhpStorm.
 * User: huye
 * Date: 2017/9/18
 * Time: 上午11:43
 */
namespace ZanPHP\Coroutine\Tests;

use ZanPHP\Coroutine\Task;
use ZanPHP\Coroutine\Tests\Task\AsyncJob;
use ZanPHP\Coroutine\Tests\Task\Coroutine;
use ZanPHP\Coroutine\Tests\Task\Error;
use ZanPHP\Coroutine\Tests\Task\Simple;
use ZanPHP\Coroutine\Tests\Task\Steps;
use ZanPHP\Coroutine\Tests\Task\YieldValues;
use ZanPHP\Coroutine\Tests\Task\Response;
use ZanPHP\Testing\UnitTest;

class TaskTest extends UnitTest {
    public function testSimpleYieldWorkFine() {
        $context = new Context();

        $job = new Simple($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $result = $context->show();
        $this->assertArrayHasKey('key',$result, 'simple job failed to set context');
        $this->assertEquals('simple value', $context->get('key'), 'simple job get wrong context value');

        $taskData = $task->getResult();
        $this->assertEquals('simple job done', $taskData, 'get simple task final output fail');
    }

    public function testCoroutineWorkFine() {
        $context = new Context();

        $job = new Coroutine($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $result = $context->show();

        $this->assertArrayHasKey('step1_call',$result, 'coroutine job failed to set context');
        $this->assertEquals('step1', $context->get('step1_call'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('step2_call',$result, 'coroutine job failed to set context');
        $this->assertEquals('step2', $context->get('step2_call'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('inner_call',$result, 'coroutine job failed to set context');
        $this->assertEquals('inner', $context->get('inner_call'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('step2_inner',$result, 'coroutine job failed to set context');
        $this->assertEquals('coroutine.inner()', $context->get('step2_inner'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('step1_response',$result, 'coroutine job failed to set context');
        $this->assertEquals('coroutine.step1()', $context->get('step1_response'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('step2_response',$result, 'coroutine job failed to set context');
        $this->assertEquals('coroutine.step2()', $context->get('step2_response'), 'coroutine job get wrong context value');

        $this->assertArrayHasKey('work_response',$result, 'coroutine job failed to set context');
        $this->assertEquals('coroutine.work()', $context->get('work_response'), 'coroutine job get wrong context value');

        $taskData = $task->getResult();
        $this->assertEquals('coroutine job done', $taskData, 'get coroutine task final output fail');
    }

    public function testAsyncWorkFine() {
        $context = new Context();

        $job = new AsyncJob($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $job->fakeResponse();

        $result = $context->show();

        $this->assertArrayHasKey('call()',$result, 'async job failed to set context');
        $this->assertEquals('call', $context->get('call()'), 'async job get wrong context value');

        $this->assertArrayHasKey('response',$result, 'async job failed to set context');
        $this->assertInstanceOf(Response::class,$context->get('response'),'async job get response fail');

        $response = $context->get('response');
        $responseData = $response->getData();
        $this->assertEquals(200, $response->getCode(), 'async job get wrong response');
        $this->assertEquals('ok', $response->getMessage(), 'async job get wrong response');

        $this->assertArrayHasKey('data',$responseData,'async job get wrong response');
        $this->assertEquals('rpc', $responseData['data'], 'async job get wrong response');


        $taskData = $task->getResult();
        $taskResponse = $taskData->getData();
        $this->assertEquals(200, $taskData->getCode(), 'async job get wrong response');
        $this->assertEquals('ok', $taskData->getMessage(), 'async job get wrong response');

        $this->assertArrayHasKey('data',$taskResponse,'async job get wrong response');
        $this->assertEquals('rpc', $taskResponse['data'], 'async job get wrong response');
    }

    public function testExceptionWorkFine() {
        $context = new Context();

        $job = new Error($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $result = $context->show();

        $this->assertArrayHasKey('step1_response',$result, 'exception job failed to set context');
        $this->assertEquals('step1', $context->get('step1_response'), 'exception job get wrong context value');

        $this->assertArrayHasKey('exception_code',$result, 'exception job failed to set context');
        $this->assertEquals(404, $context->get('exception_code'), 'exception job get wrong context value');

        $this->assertArrayHasKey('exception_msg',$result, 'exception job failed to set context');
        $this->assertEquals('ErrorException Msg', $context->get('exception_msg'), 'exception job get wrong context value');

        $this->assertArrayHasKey('exception',$result, 'exception job failed to set context');
        $this->assertEquals('ZanPHP\Coroutine\Tests\Task\ErrorException', $context->get('exception'), 'exception job get wrong context value');

        //$this->assertArrayNotHasKey('work_response',$result, 'exception job failed to set context');

        $taskData = $task->getResult();
        $this->assertEquals('Error.catch.exception', $taskData, 'get exception task final output fail');
    }

    public function testStepsWorkFine()
    {
        $context = new Context();

        $job = new Steps($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $result = $context->show();

        $this->assertArrayHasKey('result',$result, 'steps job failed to set context');
        $this->assertEquals('stepN', $context->get('result'), 'steps job get wrong context value');

        $taskData = $task->getResult();
        $this->assertEquals('stepN', $taskData, 'get steps task final output fail');

    }

    public function testYieldValuesWorkFine()
    {
        $context = new Context();

        $job = new YieldValues($context);
        $coroutine = $job->run();

        $task = new Task($coroutine);
        $task->run();

        $job->fakeResponse();

        $result = $context->show();

        $this->assertArrayHasKey('step4_response',$result, 'YieldValues job failed to set context');
        $this->assertEquals('coroutine.step44444444()', $context->get('step4_response'), 'YieldValues job get wrong context value');

        $taskData = $task->getResult();
        $this->assertEquals('YieldValues job done', $taskData, 'get YieldValues task final output fail');

    }
}