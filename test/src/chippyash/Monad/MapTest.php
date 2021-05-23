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
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testYouCannotCreateAnEmptyMap()
    {
        $this->expectException(\RuntimeException::class);
        new Map();
    }

    public function testMapsRequireStringKeys()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('value is not a hashed array');
        new Map(['a','b']);
    }

    public function testYouCanConstructAnEmptyMapIfYouPassAType()
    {
        $this->assertInstanceOf(Map::class, new Map([], 'string'));
        $this->assertInstanceOf(Map::class, new Map([], 'Monad\Identity'));
    }

    public function testAppendingToAMapReturnsANewMap()
    {
        $orig = new Map([], 'string');
        $new = $orig->append(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $new->toArray());
    }

    public function testAppendingToAMapWithUnhashedValuesThrowsAnException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('value is not a hashed array');
        $orig = new Map([], 'string');
        $orig->append(['bar']);
    }

    public function testVunionMethodIsNotSupportedForMaps()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('vUnion is not a supported method for Maps');
        $setA = Map::create(['a' =>0, 'b' => 0]);
        $setB = Map::create(['c' =>0, 'd' => 0]);
        $setA->vUnion($setB);
    }

    public function testVintersectMethodIsNotSupportedForMaps()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('vIntersect is not a supported method for Maps');
        $setA = Map::create(['a' =>0, 'b' => 0]);
        $setB = Map::create(['c' =>0, 'd' => 0]);
        $setA->vIntersect($setB);
    }

    public function testVdiffMethodIsNotSupportedForMaps()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('vDiff is not a supported method for Maps');
        $setA = Map::create(['a' =>0, 'b' => 0]);
        $setB = Map::create(['c' =>0, 'd' => 0]);
        $setA->vDiff($setB);
    }
}
