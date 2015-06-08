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
    public function testYouCannotConstructAnOptionDirectly()
    {
        $refl = new \ReflectionClass('Monad\Option');
        $this->assertNull($refl->getConstructor());
        $this->assertTrue($refl->isAbstract());
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

    public function testYouCanReplaceNoneTestByCallingCreateWithAdditionalParameter()
    {
        $sut = Option::create(true, false);
        $this->assertInstanceOf('Monad\Option\Some', $sut);
        $sut1 = Option::create(false, false);
        $this->assertInstanceOf('Monad\Option\None', $sut1);
    }
}
