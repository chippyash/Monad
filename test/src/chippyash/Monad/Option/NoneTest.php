<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Option\None;
use PHPUnit\Framework\TestCase;

class NoneTest extends TestCase
{
    public function testYouCanConstructANone()
    {
        $this->assertInstanceOf(None::class, new None());
    }

    public function testYouCanConstructANoneWithAParameterAndItWillStillBeNone()
    {
        $this->assertInstanceOf(None::class, new None('foo'));
    }

    public function testCreateWillReturnANone()
    {
        $this->assertInstanceOf(None::class, None::create());
    }

    public function testBindingANoneReturnsANone()
    {
        $none = new None();
        $this->assertInstanceOf(None::class, $none->bind(function(){}));
    }

    public function testCallingGetOnANoneThrowsARuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        None::create()->value();
    }
}
