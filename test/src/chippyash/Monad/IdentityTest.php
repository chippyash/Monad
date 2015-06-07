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

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var Identity
     */
    protected $sut;
    
    protected function setUp()
    {
        $this->sut = new Identity('foo');
    }
    
    public function testYouCanCreateAnIdentityStatically()
    {
        $this->assertInstanceOf('Monad\Identity', Identity::create('bar'));
    }

    public function testCreatingAnIdentityStaticallyWithAnIdentityParameterWillReturnTheParameter()
    {
        $this->assertEquals($this->sut, Identity::create($this->sut));
    }

    public function testYouCanMapAFunctionOnAnIdentity()
    {
        $func = function ($value) {
            return $value . 'bar';
        };
        $this->assertEquals('foobar', $this->sut->map($func)->get());

        $identity = new Identity($this->sut);
        $this->assertEquals('foobar', $identity->map($func)->get());
    }

    public function testMapCanTakeOptionalAdditionalParameters()
    {
        $func = function ($value, $fudge) {
            return $value . $fudge;
        };
        $this->assertEquals('foobar', $this->sut->map($func, ['bar'])->get());

        $identity = new Identity($this->sut);
        $this->assertEquals('foobar', $identity->map($func, ['bar'])->get());
    }

    public function testMagicInvokeProxiesToMapMethodIfPassedAClosure()
    {
        $func = function ($value) {
            return $value . 'bar';
        };
        $sut = $this->sut;
        $this->assertEquals('foobar', $sut($func)->get());
    }

    public function testMagicInvokeProxiesToGetMethodIfPassedNoParameters()
    {
        $sut = $this->sut;
        $this->assertEquals('foo', $sut());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCallingMagicInvokeWillThrowExceptionIfNoMethodIsExecutable()
    {
        $sut = $this->sut;
        $sut('foo');
    }

    public function testYouCanChainMapMethodsTogether()
    {
        $sut = new Identity(10);
        $val = $sut
            ->map(function($v){return $v * 10;})
            ->map(function($v, $n){return $v - $n;}, [2])
            ->get();
        $this->assertEquals(98, $val);
    }


    public function testMappingOnAnIdentityWithAClosureValueWillEvaluateTheValue()
    {
        $sut = new Identity(function(){return 'foo';});
        $func = function ($value) {
            return $value . 'bar';
        };
        $this->assertEquals('foobar', $sut($func)->get());
    }
}
