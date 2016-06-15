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

class MutableCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var MutableCollection
     */
    protected $sut;

    protected function setUp()
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

}
