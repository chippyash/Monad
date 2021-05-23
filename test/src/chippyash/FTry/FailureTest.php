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
use PHPUnit\Framework\TestCase;

class FailureTest extends TestCase
{
    public function testCanConstructIfValueIsException()
    {
        $this->assertInstanceOf(Failure::class, new Failure(new \Exception()));
    }

    public function testCreateCreatesAFailureIfValueIsException()
    {
        $this->assertInstanceOf(Failure::class, Failure::create(new \Exception()));
    }

    public function testCreateCreatesAFailureIfValueIsNotAnException()
    {
        $this->assertInstanceOf(Failure::class, Failure::create('foo'));
    }

    public function testBindReturnsFailureWithSameValue()
    {
        $exc = new \Exception();
        $fail =  Failure::create($exc);
        $this->assertInstanceOf(Failure::class, $fail);
        $this->assertInstanceOf(Failure::class, $fail->bind(function(){}));
        $this->assertEquals($exc, $fail->bind(function(){})->value());
    }

    public function testCallingPassWillThrowAnException()
    {
        $this->expectException(\RuntimeException::class);
        Failure::create('foo')->pass();
    }

    public function testCallingIsSuccessWillReturnFalse()
    {
        $this->assertFalse(Failure::create(new \Exception())->isSuccess());
    }
}
