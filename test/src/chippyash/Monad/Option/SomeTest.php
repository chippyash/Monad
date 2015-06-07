<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Option\Some;

class SomeTest extends \PHPUnit_Framework_TestCase
{
    public function testYouCanConstructASomeIfYouHaveAValueForIt()
    {
        $this->assertInstanceOf('Monad\Option\Some', new Some('foo'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testYouCannotConstructASomeWithNoValue()
    {
        $this->assertInstanceOf('Monad\Option\Some', new Some());
    }

    public function testYouCanGetAValueFromASome()
    {
        $this->assertEquals('foo', (new Some('foo'))->get());
    }
}
