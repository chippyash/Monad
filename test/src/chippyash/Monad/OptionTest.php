<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Option;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionWithAValueProxiesASome()
    {
        $sut = new Option('foo');
        $rProp = new \ReflectionProperty($sut, 'value');
        $rProp->setAccessible(true);
        $value = $rProp->getValue($sut);

        $this->assertInstanceOf('Monad\Option\Some', $value);
        $this->assertEquals('foo', $sut->get());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testConstructionWithNoValueProxiesANone()
    {
        $sut = new Option();
        $rProp = new \ReflectionProperty($sut, 'value');
        $rProp->setAccessible(true);
        $value = $rProp->getValue($sut);

        $this->assertInstanceOf('Monad\Option\None', $value);
        $sut->get();
    }

    public function testCreatingWithAValueReturnsASome()
    {
        $sut = Option::create('foo');
        $this->assertInstanceOf('Monad\Option\Some', $sut);
    }

    public function testCreatingWithNoValueOrNullReturnsANone()
    {
        $sut = Option::create();
        $this->assertInstanceOf('Monad\Option\None', $sut);
        $sut = Option::create(null);
        $this->assertInstanceOf('Monad\Option\None', $sut);
    }

    public function testYouCanReplaceNoneTestByCallingOption()
    {
        $sut = Option::option(true, false);
        $this->assertInstanceOf('Monad\Option\Some', $sut);
        $sut1 = Option::option(false, false);
        $this->assertInstanceOf('Monad\Option\None', $sut1);
    }
}
