<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\test;


use Monad\FTry\Success;

class SuccessTest extends \PHPUnit_Framework_TestCase
{
    public function testYouCanConstructASuccessIfYouHaveAValueForIt()
    {
        $this->assertInstanceOf('Monad\FTry\Success', new Success('foo'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testYouCannotConstructASuccessWithAnException()
    {
        new Success(new \Exception());
    }

    public function testBindingASuccessWithSomethingThatDoesNotThrowAnExceptionWillReturnSuccess()
    {
        $sut = new Success('foo');
        $this->assertInstanceOf('Monad\FTry\Success', $sut->bind(function(){return true;}));
    }

    public function testBindingASuccessWithSomethingThatThrowsAnExceptionWillReturnAFailure()
    {
        $sut = new Success('foo');
        $this->assertInstanceOf('Monad\FTry\Failure', $sut->bind(function(){throw new \Exception();}));
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
