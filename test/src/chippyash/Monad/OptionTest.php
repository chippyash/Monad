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
use Monad\Option\None;
use Monad\Option\Some;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testYouCannotConstructAnOptionDirectly()
    {
        $refl = new \ReflectionClass(Option::class);
        $this->assertNull($refl->getConstructor());
        $this->assertTrue($refl->isAbstract());
    }

    public function testCreatingWithAValueReturnsASome()
    {
        $sut = Option::create('foo');
        $this->assertInstanceOf(Some::class, $sut);
    }

    public function testCreatingWithNoValueOrNullReturnsANone()
    {
        $sut = Option::create();
        $this->assertInstanceOf(None::class, $sut);
        $sut = Option::create(null);
        $this->assertInstanceOf(None::class, $sut);
    }

    public function testYouCanReplaceNoneTestByCallingCreateWithAdditionalParameter()
    {
        $sut = Option::create(true, false);
        $this->assertInstanceOf(Some::class, $sut);
        $sut1 = Option::create(false, false);
        $this->assertInstanceOf(None::class, $sut1);
    }

    public function testGetOrElseWillReturnOptionValueIfOptionIsASome()
    {
        $sut = Option::create(true);
        $this->assertTrue($sut->getOrElse(false));
    }

    public function testGetOrElseWillReturnElseValueIfOptionIsANone()
    {
        $sut = Option::create(true, true);
        $this->assertFalse($sut->getOrElse(false));
    }
}
