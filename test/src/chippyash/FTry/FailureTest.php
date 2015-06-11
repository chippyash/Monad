<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\test;


use Monad\FTry\Failure;

class FailureTest extends \PHPUnit_Framework_TestCase
{
    public function testCanConstructIfValueIsException()
    {
        $this->assertInstanceOf('Monad\FTry\Failure', new Failure(new \Exception()));
    }

    public function testCreateCreatesAFailureIfValueIsException()
    {
        $this->assertInstanceOf('Monad\FTry\Failure', Failure::create(new \Exception()));
    }

    public function testCreateCreatesAFailureIfValueIsNotAnException()
    {
        $this->assertInstanceOf('Monad\FTry\Failure', Failure::create('foo'));
    }

    public function testBindReturnsFailureWithSameValue()
    {
        $exc = new \Exception();
        $fail =  Failure::create($exc);
        $this->assertInstanceOf('Monad\FTry\Failure', $fail);
        $this->assertInstanceOf('Monad\FTry\Failure', $fail->bind(function(){}));
        $this->assertEquals($exc, $fail->bind(function(){})->value());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCallingPassWillThrowAnException()
    {
        Failure::create('foo')->pass();
    }

    public function testCallingIsSuccessWillReturnFalse()
    {
        $this->assertFalse(Failure::create(new \Exception())->isSuccess());
    }
}
