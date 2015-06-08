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

class NoneTest extends \PHPUnit_Framework_TestCase
{
    public function testYouCanConstructANone()
    {
        $this->assertInstanceOf('Monad\Option\None', new None());
    }

    public function testYouCanConstructANoneWithAParameterAndItWillStillBeNone()
    {
        $this->assertInstanceOf('Monad\Option\None', new None('foo'));
    }

    public function testCreateWillReturnANone()
    {
        $this->assertInstanceOf('Monad\Option\None', None::create());
    }

    public function testMappingANoneReturnsANone()
    {
        $none = new None();
        $this->assertInstanceOf('Monad\Option\None', $none->map(function(){}));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCallingGetOnANoneThrowsARuntimeException()
    {
        None::create()->value();
    }
}
