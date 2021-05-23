<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    /**
     * System Under Test
     * @var Identity
     */
    protected $sut;
    
    protected function setUp(): void
    {
        $this->sut = new Identity('foo');
    }
    
    public function testYouCanCreateAnIdentityStatically()
    {
        $this->assertInstanceOf(Identity::class, Identity::create('bar'));
    }

    public function testCreatingAnIdentityWithAnIdentityParameterWillReturnTheParameter()
    {
        $this->assertEquals($this->sut, Identity::create($this->sut));
    }

    public function testCreatingAnIdentityWithANonIdentityParameterWillReturnAnIdentityContainingTheParameterAsValue()
    {
        $sut = Identity::create('foo');
        $this->assertInstanceOf(Identity::class, $sut);
        $this->assertEquals('foo', $sut->value());

        $sut1 = Identity::create(function($a){return $a;});
        $this->assertInstanceOf(Identity::class, $sut1);
        $this->assertInstanceOf(\Closure::class, $sut1->value());
    }

    public function testYouCanBindAFunctionOnAnIdentity()
    {
        $func = function ($value) {
            return $value . 'bar';
        };
        $this->assertEquals('foobar', $this->sut->bind($func)->value());

        $identity = new Identity($this->sut);
        $this->assertEquals('foobar', $identity->bind($func)->value());
    }

    public function testBindCanTakeOptionalAdditionalParameters()
    {
        $func = function ($value, $fudge) {
            return $value . $fudge;
        };
        $this->assertEquals('foobar', $this->sut->bind($func, ['bar'])->value());

        $identity = new Identity($this->sut);
        $this->assertEquals('foobar', $identity->bind($func, ['bar'])->value());
    }

    public function testYouCanChainBindMethodsTogether()
    {
        $sut = new Identity(10);
        $val = $sut
            ->bind(function($v){return $v * 10;})
            ->bind(function($v, $n){return $v - $n;}, [2])
            ->value();
        $this->assertEquals(98, $val);
    }


    public function testBindingOnAnIdentityWithAClosureValueWillEvaluateTheValue()
    {
        $sut = new Identity(function(){return 'foo';});
        $func = function ($value) {
            return $value . 'bar';
        };
        $this->assertEquals('foobar', $sut->bind($func)->value());
    }

    public function testYouCanFlattenAnIdentityValueToItsBaseType()
    {
        $sut = new Identity(function(){return 'foo';});
        $func = function ($value) {
            return $value . 'bar';
        };
        $this->assertEquals('foobar', $sut->bind($func)->flatten());
    }
}
