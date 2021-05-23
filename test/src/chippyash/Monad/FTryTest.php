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
use Monad\FTry\Failure;
use Monad\FTry\Success;
use Monad\Identity;
use PHPUnit\Framework\TestCase;

class FTryTest extends TestCase
{
    public function testCreatingAnFTryWithANonExceptionWillReturnASuccess()
    {
        $this->assertInstanceOf(Success::class, FTry::create('foo'));
        $this->assertEquals('foo', FTry::create('foo')->value());

        $this->assertInstanceOf(Success::class, FTry::create(function(){return true;}));
        $this->assertInstanceOf(\Closure::class, FTry::create(function(){return true;})->value());

        $this->assertTrue(FTry::create(function(){return true;})->flatten());
        $this->assertInstanceOf(Success::class, FTry::create(new Identity('foo')));

        $this->assertEquals('foo', FTry::create(new Identity('foo'))->flatten());
    }

    public function testCreatingAnFTryWithAnExceptionWillReturnAFailure()
    {
        $this->assertInstanceOf(Failure::class, FTry::create(new \Exception()));
        $this->assertInstanceOf(\Exception::class, FTry::create(new \Exception())->value());

        $this->assertInstanceOf(Failure::class, FTry::create(function(){throw new \Exception();}));
        $this->assertInstanceOf(\Exception::class, FTry::create(function(){throw new \Exception();})->value());

        $this->assertInstanceOf(Failure::class, FTry::create(new Identity(function(){throw new \Exception();})));
        $this->assertInstanceOf(\Exception::class, FTry::create(new Identity(function(){throw new \Exception();}))->value());
    }

    public function testTheWithMethodProxiesToCreate()
    {
        $this->assertInstanceOf(Success::class, FTry::with('foo'));
        $this->assertInstanceOf(Success::class, FTry::with(function(){return true;}));
        $this->assertInstanceOf(Success::class, FTry::with(new Identity('foo')));
        $this->assertInstanceOf(Failure::class, FTry::with(new \Exception()));
        $this->assertInstanceOf(Failure::class, FTry::with(function(){throw new \Exception();}));
        $this->assertInstanceOf(Failure::class, FTry::with(new Identity(function(){throw new \Exception();})));
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
