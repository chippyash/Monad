<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Monad;
use Monad\Monadic;
use PHPUnit\Framework\TestCase;

class MonadTest extends TestCase
{
    /**
     * System Under Test
     * @var
     */
    protected $sut;
    
    protected function setUp(): void
    {
        $this->sut = $this->getMockForAbstractClass(Monad::class);
    }

    public function testYouCanReturnAValueWhenMonadCreatedWithSimpleValue()
    {
        $this->setSutValue('foo');
        $this->assertEquals('foo', $this->sut->value());
    }

    public function testYouCanReturnAValueWhenMonadCreatedWithMonadicValue()
    {
        $bound = $this->createMonadWithValue('foo');
        $this->setSutValue($bound);
        $this->assertInstanceOf(Monadic::class, $this->sut->value());
    }

    public function testYouCanUseAClosureForValue()
    {
        $this->setSutValue(function(){return 'foo';});
        $this->assertInstanceOf(\Closure::class, $this->sut->value());
    }

    public function testFlattenWillReturnBaseType()
    {
        $this->setSutValue('foo');
        $this->assertEquals('foo', $this->sut->flatten());
        $this->setSutValue(function(){return 'foo';});
        $this->assertEquals('foo', $this->sut->flatten());
        $this->setSutValue($this->createMonadWithValue('foo'));
        $this->assertEquals('foo', $this->sut->flatten());
    }

    public function testYouCanBindAClosureOnAMonadToCreateANewMonadOfTheSameType()
    {
        $this->setSutValue('foo');
        $fn = function($value){return $value;};
        $this->assertEquals(get_class($this->sut), get_class($this->sut->bind($fn)));

        $this->setSutValue($this->createMonadWithValue('foo'));
        $this->assertEquals(get_class($this->sut), get_class($this->sut->bind($fn)));

        $this->setSutValue(function(){return 'foo';});
        $this->assertEquals(get_class($this->sut), get_class($this->sut->bind($fn)));
    }

    public function testBindCanTakeOptionalAdditionalParameters()
    {
        $fn = function ($value, $fudge) {
            return $value . $fudge;
        };
        $this->assertEquals(get_class($this->sut), get_class($this->sut->bind($fn, ['bar'])));
    }

    public function testMagicInvokeProxiesToBindMethodIfPassedAClosure()
    {
        $this->setSutValue('foo');
        $fn = function ($value) {
            return $value . 'bar';
        };
        $sut = $this->sut;
        $this->assertEquals(get_class($sut), get_class($sut($fn)));
    }

    public function testMagicInvokeProxiesToValueMethodIfPassedNoParameters()
    {
        $this->setSutValue('foo');
        $sut = $this->sut;
        $this->assertEquals('foo', $sut());
    }

    public function testCallingMagicInvokeWillThrowExceptionIfNoMethodIsExecutable()
    {
        $this->expectException(\BadMethodCallException::class);
        $sut = $this->sut;
        $sut('foo');
    }

    public function testYouCannotCreateAnAbstractMonadStatically()
    {
        $refl = new \ReflectionClass(Monad::class);
        $this->assertNull($refl->getConstructor());
    }

    /**
     * Set value on the Monad SUT - Abstract Monad does not have a constructor
     * @param $value
     */
    private function setSutValue($value)
    {
        $refl = new \ReflectionProperty($this->sut, 'value');
        $refl->setAccessible(true);
        $refl->setValue($this->sut, $value);
    }

    /**
     * Create a Mock Monad with a value
     *
     * @param mixed $value
     * @return Monad Mock Monad
     */
    private function createMonadWithValue($value)
    {
        $monad = $this->getMockForAbstractClass(Monad::class);
        $refl = new \ReflectionProperty($monad, 'value');
        $refl->setAccessible(true);
        $refl->setValue($monad, $value);

        return $monad;
    }
}
