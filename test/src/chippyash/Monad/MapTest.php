<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Monad\Test;

use Monad\Map;

class MapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testYouCannotCreateAnEmptyMap()
    {
        new Map();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage value is not a hashed array
     */
    public function testMapsRequireStringKeys()
    {
        new Map(['a','b']);
    }

    public function testYouCanConstructAnEmptyMapIfYouPassAType()
    {
        $this->assertInstanceOf('Monad\Map', new Map([], 'string'));
        $this->assertInstanceOf('Monad\Map', new Map([], 'Monad\Identity'));
    }

    public function testAppendingToAMapReturnsANewMap()
    {
        $orig = new Map([], 'string');
        $new = $orig->append(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $new->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage value is not a hashed array
     */
    public function testAppendingToAMapWithUnhashedValuesThrowsAnException()
    {
        $orig = new Map([], 'string');
        $orig->append(['bar']);
    }
}
