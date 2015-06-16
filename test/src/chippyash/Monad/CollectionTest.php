<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testYouCanConstructACollectionWithANonEmptyArray()
    {
        $this->assertInstanceOf('Monad\Collection', new Collection(['foo']));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testYouCannotConstructACollectionWithNullValues()
    {
        $this->assertInstanceOf('Monad\Collection', new Collection([null]));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testYouCannotConstructACollectionWithAnEmptyArrayAndNoTypeSpecified()
    {
        new Collection([]);
    }

    public function testYouCanConstructAnEmptyCollectionIfYouPassAType()
    {
        $this->assertInstanceOf('Monad\Collection', new Collection([], 'string'));
        $this->assertInstanceOf('Monad\Collection', new Collection([], 'Monad\Identity'));
    }

    public function testWhenConstructingACollectionYouMustHaveSameTypeValues()
    {
        $this->assertInstanceOf('Monad\Collection', new Collection(['foo','bar','baz'], 'string'));
    }

    /**
     * @ExpectedException \RuntimeException
     */
    public function testConstructingACollectionWithDissimilarTypesWillCauseAnException()
    {
        $this->assertInstanceOf('Monad\Collection', new Collection(['foo',new \StdClass(),'baz'], 'string'));
    }

    public function testYouCanCreateACollection()
    {
        $this->assertInstanceOf('Monad\Collection', Collection::create(['foo']));
    }

    public function testYouCanBindAFunctionToEachMemberOfTheCollectionAndReturnACollection()
    {
        $sut = Collection::create([2,3,4,5,6]);
        $res = $sut->bind(function($v){return $v * 2;});
        $this->assertEquals([4,6,8,10,12], $res->value());
    }

    public function testYouCanCountTheItemsInTheCollection()
    {
        $this->assertEquals(4, Collection::create([1,2,3,4])->count());
    }

    public function testYouCanGetAnIteratorForACollection()
    {
        $this->assertInstanceOf('ArrayIterator', Collection::create([1,2,3,4])->getIterator());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testYouCannotUnsetACollectionMember()
    {
        Collection::create([1,2,3])->offsetUnset(2);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testYouCannotSetACollectionMember()
    {
        Collection::create([1,2,3])->offsetSet(2, 6);
    }

    public function testYouCanGetACollectionMemberAsAnArrayOffset()
    {
        $sut = Collection::create([1,2,3]);
        $this->assertEquals(2, $sut->offsetGet(1));
        $this->assertEquals(2, $sut[1]);
    }

    public function testYouCanTestIfACollectionMemberExistsAsAnArrayOffset()
    {
        $sut = Collection::create([1,2,3]);
        $this->assertTrue(isset($sut[1]));
        $this->assertFalse(isset($sut[99]));
    }

    public function testYouCanCreateACollectionOfCollections()
    {
        $s1 = Collection::create([1,2,3]);
        $s2 = Collection::create([5,6,7]);
        $s3 = Collection::create([$s1, $s2]);
        $this->assertInstanceOf('Monad\Collection', $s3);
    }

    public function testFlatteningACollectionOfCollectionsWillReturnPHPArrayOfArrays()
    {
        $s1 = Collection::create([1,2,3]);
        $s2 = Collection::create([5,6,7]);
        $flattened = Collection::create([$s1, $s2])->flatten();
        $this->assertInternalType('array', $flattened);
        foreach ($flattened as $value) {
            $this->assertInternalType('array', $value);
        }
    }
}
