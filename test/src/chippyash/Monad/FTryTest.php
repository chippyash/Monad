<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;


use Monad\FTry;
use Monad\Identity;

class FTryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingAnFTryWithANonExceptionWillReturnASuccess()
    {
        $this->assertInstanceOf('Monad\FTry\Success', FTry::create('foo'));
        $this->assertEquals('foo', FTry::create('foo')->value());

        $this->assertInstanceOf('Monad\FTry\Success', FTry::create(function(){return true;}));
        $this->assertInstanceOf('Closure', FTry::create(function(){return true;})->value());

        $this->assertTrue(FTry::create(function(){return true;})->flatten());
        $this->assertInstanceOf('Monad\FTry\Success', FTry::create(new Identity('foo')));

        $this->assertEquals('foo', FTry::create(new Identity('foo'))->flatten());
    }

    public function testCreatingAnFTryWithAnExceptionWillReturnAFailure()
    {
        $this->assertInstanceOf('Monad\FTry\Failure', FTry::create(new \Exception()));
        $this->assertInstanceOf('Exception', FTry::create(new \Exception())->value());

        $this->assertInstanceOf('Monad\FTry\Failure', FTry::create(function(){throw new \Exception();}));
        $this->assertInstanceOf('Exception', FTry::create(function(){throw new \Exception();})->value());

        $this->assertInstanceOf('Monad\FTry\Failure', FTry::create(new Identity(function(){throw new \Exception();})));
        $this->assertInstanceOf('Exception', FTry::create(new Identity(function(){throw new \Exception();}))->value());
    }

    public function testTheWithMethodProxiesToCreate()
    {
        $this->assertInstanceOf('Monad\FTry\Success', FTry::with('foo'));
        $this->assertInstanceOf('Monad\FTry\Success', FTry::with(function(){return true;}));
        $this->assertInstanceOf('Monad\FTry\Success', FTry::with(new Identity('foo')));

        $this->assertInstanceOf('Monad\FTry\Failure', FTry::with(new \Exception()));
        $this->assertInstanceOf('Monad\FTry\Failure', FTry::with(function(){throw new \Exception();}));
        $this->assertInstanceOf('Monad\FTry\Failure', FTry::with(new Identity(function(){throw new \Exception();})));
    }

    public function testGetOrElseWillReturnFTryValueIfOptionIsASuccess()
    {
        $sut = FTry::create(true);
        $this->assertTrue($sut->getOrElse(false));
    }

    public function testGetOrElseWillReturnElseValueIfFTryIsAFailure()
    {
        $sut = FTry::create(new \Exception());
        $this->assertFalse($sut->getOrElse(false));
    }

}
