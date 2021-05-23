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
use Monad\FTry\Success;
use PHPUnit\Framework\TestCase;

class SuccessTest extends TestCase
{
    public function testYouCanConstructASuccessIfYouHaveAValueForIt()
    {
        $this->assertInstanceOf(Success::class, new Success('foo'));
    }

    public function testYouCannotConstructASuccessWithAnException()
    {
        $this->expectException(\RuntimeException::class);
        new Success(new \Exception());
    }

    public function testBindingASuccessWithSomethingThatDoesNotThrowAnExceptionWillReturnSuccess()
    {
        $sut = new Success('foo');
        $this->assertInstanceOf(Success::class, $sut->bind(function(){return true;}));
    }

    public function testBindingASuccessWithSomethingThatReturnsASuccessWillFlattenTheValue()
    {
      $sut = new Success('foo');
      $binded = $sut->bind(function () {
        return new Success('bar');
      });
      $this->assertInstanceOf(Success::class, $binded);
      $this->assertEquals('bar', $binded->value());
    }

    public function testBindingASuccessWithSomethingThatThrowsAnExceptionWillReturnAFailure()
    {
        $sut = new Success('foo');
        $this->assertInstanceOf(Failure::class, $sut->bind(function(){throw new \Exception();}));
    }

    public function testYouCanGetAValueFromASuccess()
    {
        $sut = new Success('foo');
        $this->assertEquals('foo', $sut->value());
    }

    public function testCallingIsSuccessWillReturnTrue()
    {
        $this->assertTrue(Success::create('foo')->isSuccess());
    }
}
