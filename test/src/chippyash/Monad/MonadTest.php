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

class MonadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var
     */
    protected $sut;
    
    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass('Monad\Monad');
    }

    public function testGetReturnsValueWhenMonadCreatedWithSimpleValue()
    {
        $this->setSutValue('foo');
        $this->assertEquals('foo', $this->sut->get());
    }

    public function testGetReturnsBoundMonadWhenMonadCreatedWithMonadicValue()
    {
        $bound = $this->createMonadWithValue('foo');
        $this->setSutValue($bound);
        $this->assertInstanceOf('Monad\Monadic', $this->sut->get());
    }

    public function testYouCanUseAClosureForValue()
    {
        $this->setSutValue(function(){return 'foo';});
        $this->assertInstanceOf('Closure', $this->sut->get());
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

    /**
     * Set value on the Monad - Abstract Monad does not have a constructor
     * @param $value
     */
    private function setSutValue($value)
    {
        $refl = new \ReflectionProperty($this->sut, 'value');
        $refl->setAccessible(true);
        $refl->setValue($this->sut, $value);
    }

    private function createMonadWithValue($value)
    {
        $monad = $this->getMockForAbstractClass('Monad\Monad');
        $refl = new \ReflectionProperty($monad, 'value');
        $refl->setAccessible(true);
        $refl->setValue($monad, $value);

        return $monad;
    }
}
