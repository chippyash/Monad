<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2016, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\MutableCollection;
use PHPUnit\Framework\TestCase;

class MutableCollectionTest extends TestCase
{
    /**
     * System Under Test
     * @var MutableCollection
     */
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new MutableCollection(['foo', 'bar', 'baz']);
    }

    public function testYouCanUnsetAMutableCollectionMember()
    {
        unset($this->sut[2]);
        $this->assertEquals(['foo', 'bar'], $this->sut->toArray());
    }

    public function testYouCanSetAMutableCollectionMember()
    {
        $this->sut[2] = 'bop';
        $this->assertEquals(['foo', 'bar', 'bop'], $this->sut->toArray());
    }

    /**
     * Simple test to ensure that we get back a MutableCollection as a result
     * of calling an underlaying Collection method as they all use `new static(...)`
     */
    public function testCallingOneOfTheUnderlayingCollectionMethodsReturnsAMutableCollection()
    {
        $this->assertInstanceOf(MutableCollection::class, $this->sut->flip());
    }
}
