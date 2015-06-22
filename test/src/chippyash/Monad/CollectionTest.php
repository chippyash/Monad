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
     * @expectedException \RuntimeException
     */
    public function testConstructingACollectionWithDissimilarTypesWillCauseAnException()
    {
        new Collection(['foo',new \StdClass(),'baz'], 'string');
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

    public function testYouCanGetTheDifferenceBetweenTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $this->assertEquals([1,2,3], $s1->diff($s2)->flatten());
    }

    public function testYouCanChainDiffMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $s3 = Collection::create([1]);
        $s4 = Collection::create([9]);

        $this->assertEquals([2,3], array_values($s1->diff($s2)->diff($s3)->diff($s4)->flatten()));
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToDiffMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([1,2,3], $s1->diff($s2, $f)->flatten());
    }

    public function testYouCanGetTheIntersectionOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $this->assertEquals([6,7], array_values($s1->intersect($s2)->flatten()));
    }

    public function testYouCanChainIntersectMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $s3 = Collection::create([7]);

        $this->assertEquals([7], array_values($s1->intersect($s2)->intersect($s3)->flatten()));
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToIntersectMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([6, 7], array_values($s1->intersect($s2, $f)->flatten()));
    }

    public function testYouCanGetTheUnionOfValuesOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([3, 6, 7, 8]);
        $this->assertEquals([1,2,3,6,7,8], array_values($s1->vUnion($s2)->flatten()));
    }

    public function testYouCanChainTheUnionOfValuesOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([3, 6, 7, 8]);
        $s3 = Collection::create([7, 8, 9, 10]);
        $this->assertEquals([1,2,3,6,7,8,9,10], array_values($s1->vUnion($s2)->vUnion($s3)->flatten()));
    }

    public function testYouCanGetTheUnionOfKeysOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([0, 0, 3, 6, 7, 8]);
        $this->assertEquals([0=>1,1=>2,2=>3,3=>6,4=>7, 5=>8], $s1->kUnion($s2)->flatten());
    }

    public function testYouCanChainTheUnionOfKeysOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([0, 0, 3, 6, 7, 8]);
        $s3 = Collection::create([0, 0, 0, 7, 8, 9, 10]);
        $this->assertEquals(
            [0=>1, 1=>2, 2=>3, 3=>6,4=>7, 5=>8, 6=>10],
            $s1->kUnion($s2)->kUnion($s3)->flatten()
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPerformingAValueUnionTheDissimilarCollectionsWillThrowAnException()
    {
        (new Collection([],'string'))->vUnion(new Collection([1]));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPerformingAKeyUnionTheDissimilarCollectionsWillThrowAnException()
    {
        (new Collection([],'string'))->kUnion(new Collection([1]));
    }

    public function testTheHeadOfACollectionIsItsFirstMember()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals([1], $s1->head()->flatten());
    }

    public function testTheTailOfACollectionIsAllButItsFirstMember()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals([2, 3, 6, 7], $s1->tail()->flatten());
    }
}
