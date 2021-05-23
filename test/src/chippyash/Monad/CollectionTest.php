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
use Monad\Monad;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testYouCanConstructACollectionWithANonEmptyArray()
    {
        $this->assertInstanceOf(Collection::class, new Collection(['foo']));
    }

    public function testYouCannotConstructACollectionWithNullValues()
    {
        $this->expectException(\RuntimeException::class);
        $this->assertInstanceOf(Collection::class, new Collection([null]));
    }

    public function testYouCannotConstructACollectionWithAnEmptyArrayAndNoTypeSpecified()
    {
        $this->expectException(\RuntimeException::class);
        new Collection([]);
    }

    public function testYouCanConstructAnEmptyCollectionIfYouPassAType()
    {
        $this->assertInstanceOf(Collection::class, new Collection([], 'string'));
        $this->assertInstanceOf(Collection::class, new Collection([], Monad::class));
    }

    public function testWhenConstructingACollectionYouMustHaveSameTypeValues()
    {
        $this->assertInstanceOf(Collection::class, new Collection(['foo','bar','baz'], 'string'));
    }

    public function testConstructingACollectionWithDissimilarTypesWillCauseAnException()
    {
        $this->expectException(\RuntimeException::class);
        new Collection(['foo',new \StdClass(),'baz'], 'string');
    }

    public function testYouCanCreateACollection()
    {
        $this->assertInstanceOf(Collection::class, Collection::create(['foo']));
    }

    public function testTheValueOfACollectionIsTheCollection()
    {
        $collection = Collection::create(['foo']);
        $this->assertEquals($collection, $collection->value());
    }

    public function testYouCanBindAFunctionToTheEntireCollectionAndReturnACollection()
    {
        $sut = Collection::create([2,3,4,5,6]);
        //function returns a single value - converted to a collection
        $f = function($c){
            return $c[0];
        };
        $this->assertEquals([2], $sut->bind($f)->getArrayCopy());
        //function returns a single value - converted to collection
        $f2 = function($c){
            return 'foo';
        };
        $this->assertEquals(['foo'], $sut->bind($f2)->getArrayCopy());
        //function returns a collection
        $f3 = function($c) {
            return new Collection(array_flip($c->toArray()));
        };
        $this->assertEquals([2=>0, 3=>1, 4=>2, 5=>3, 6=>4], $sut->bind($f3)->getArrayCopy());
        //function returns and array - converted to a collection
        $f4 = function($c){
            return $c->toArray();
        };
        $this->assertEquals([2,3,4,5,6], $sut->bind($f4)->getArrayCopy());
    }

    public function testYouCanBindAFunctionToEachMemberOfTheCollectionAndReturnACollection()
    {
        $sut = Collection::create([2,3,4,5,6]);
        $res = $sut->each(function($v){return $v * 2;});
        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals([4,6,8,10,12], $res->getArrayCopy());
    }

    public function testYouCanCountTheItemsInTheCollection()
    {
        $this->assertEquals(4, Collection::create([1,2,3,4])->count());
    }

    public function testYouCanGetAnIteratorForACollection()
    {
        $this->assertInstanceOf('ArrayIterator', Collection::create([1,2,3,4])->getIterator());
    }

    public function testYouCannotUnsetACollectionMember()
    {
        $this->expectException(\BadMethodCallException::class);
        Collection::create([1,2,3])->offsetUnset(2);
    }

    public function testYouCannotSetACollectionMember()
    {
        $this->expectException(\BadMethodCallException::class);
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
        $this->assertInstanceOf(Collection::class, $s3);
    }

    public function testFlatteningACollectionOfCollectionsWillReturnACollection()
    {
        $s1 = Collection::create([1,2,3]);
        $s2 = Collection::create([5,6,7]);
        $flattened = Collection::create([$s1, $s2])->flatten();
        $this->assertInstanceOf(Collection::class, $flattened);
        foreach ($flattened as $value) {
            $this->assertInstanceOf(Collection::class, $value);
        }
    }

    public function testYouCanGetTheDifferenceOfValuesBetweenTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $this->assertEquals([1,2,3], $s1->vDiff($s2)->flatten()->toArray());
    }

    public function testYouCanGetTheDifferenceOfKeysBetweenTwoCollections()
    {
        $s1 = Collection::create([1 => 0, 2 => 0, 3 => 0, 6 => 0, 7 => 0]);
        $s2 = Collection::create([6 => 0,7 => 0]);
        $this->assertEquals([1 => 0,2 => 0,3 => 0], $s1->kDiff($s2)->flatten()->toArray());
    }

    public function testYouCanChainVDiffMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $s3 = Collection::create([1]);
        $s4 = Collection::create([9]);

        $this->assertEquals([2,3], array_values($s1->vDiff($s2)->vDiff($s3)->vDiff($s4)->flatten()->toArray()));
    }

    public function testYouCanChainKDiffMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1 => 0, 2 => 0, 3 => 0, 6 => 0, 7 => 0]);
        $s2 = Collection::create([6 => 0, 7 => 0]);
        $s3 = Collection::create([1 => 0]);
        $s4 = Collection::create([9 => 0]);

        $this->assertEquals([2 => 0, 3 => 0], $s1->kDiff($s2)->kDiff($s3)->kDiff($s4)->flatten()->toArray());
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToVDiffMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([1,2,3], $s1->vDiff($s2, $f)->flatten()->toArray());
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToKDiffMethod()
    {
        $s1 = Collection::create([1 => 0, 2 => 0, 3 => 0, 6 => 0, 7 => 0]);
        $s2 = Collection::create([6 => 0,7 => 0]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([1 => 0,2 => 0,3 => 0], $s1->kDiff($s2, $f)->flatten()->toArray());
    }

    public function testYouCanGetTheIntersectionOfTwoCollectionsByValue()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $this->assertEquals([6,7], array_values($s1->vIntersect($s2)->flatten()->toArray()));
    }

    public function testYouCanGetTheIntersectionOfTwoCollectionsByKey()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $this->assertEquals([1,2], array_values($s1->kIntersect($s2)->flatten()->toArray()));
    }

    public function testYouCanChainValueIntersectMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $s3 = Collection::create([7]);

        $this->assertEquals([7], array_values($s1->vIntersect($s2)->vIntersect($s3)->flatten()->toArray()));
    }

    public function testYouCanChainKeyIntersectMethodsToActOnArbitraryNumbersOfCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $s3 = Collection::create([7]);

        $this->assertEquals([0=>1], array_values($s1->kIntersect($s2)->kIntersect($s3)->flatten()->toArray()));
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToTheValueIntersectMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([6, 7], array_values($s1->vIntersect($s2, $f)->flatten()->toArray()));
    }

    public function testYouCanSupplyAnOptionalComparatorFunctionToTheKeyIntersectMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([6,7]);
        $f = function($a, $b){
            return ($a<$b ? -1 : ($a>$b ? 1 : 0));
        };
        $this->assertEquals([0=>1, 1=>2], array_values($s1->kIntersect($s2, $f)->flatten()->toArray()));
    }

    public function testYouCanGetTheUnionOfValuesOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([3, 6, 7, 8]);
        $this->assertEquals([1,2,3,6,7,8], array_values($s1->vUnion($s2)->flatten()->toArray()));
    }

    public function testYouCanChainTheUnionOfValuesOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([3, 6, 7, 8]);
        $s3 = Collection::create([7, 8, 9, 10]);
        $this->assertEquals([1,2,3,6,7,8,9,10], array_values($s1->vUnion($s2)->vUnion($s3)->flatten()->toArray()));
    }

    public function testYouCanGetTheUnionOfKeysOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([0, 0, 3, 6, 7, 8]);
        $this->assertEquals([0=>1,1=>2,2=>3,3=>6,4=>7, 5=>8], $s1->kUnion($s2)->flatten()->toArray());
    }

    public function testYouCanChainTheUnionOfKeysOfTwoCollections()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = Collection::create([0, 0, 3, 6, 7, 8]);
        $s3 = Collection::create([0, 0, 0, 7, 8, 9, 10]);
        $this->assertEquals(
            [0=>1, 1=>2, 2=>3, 3=>6,4=>7, 5=>8, 6=>10],
            $s1->kUnion($s2)->kUnion($s3)->flatten()->toArray()
        );
    }

    public function testPerformingAValueUnionWithDissimilarCollectionsWillThrowAnException()
    {
        $this->expectException(\RuntimeException::class);
        (new Collection([],'string'))->vUnion(new Collection([1]));
    }

    public function testPerformingAKeyUnionWithDissimilarCollectionsWillThrowAnException()
    {
        $this->expectException(\RuntimeException::class);
        (new Collection([],'string'))->kUnion(new Collection([1]));
    }

    public function testTheHeadOfACollectionIsItsFirstMember()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals([1], $s1->head()->flatten()->toArray());
    }

    public function testTheTailOfACollectionIsAllButItsFirstMember()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals([2, 3, 6, 7], $s1->tail()->flatten()->toArray());
    }

    public function testYouCanFilterACollectionWithAClosure()
    {
        $f = function($v){return $v>3;};
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals([3=>6, 4=>7], $s1->filter($f)->toArray());
    }

    public function testYouCanReduceACollectionToASingleValueWithAClosure()
    {
        $f = function($v, $carry){return $carry + $v;};
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals(29, $s1->reduce($f, 10));
    }

    public function testYouCanReferenceACollectionAsThoughItWasAnArray()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals(2, $s1[1]);
    }

    public function testValueMethodProxiesToCollectionGetArrayCopyMethod()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $this->assertEquals($s1->toArray(), $s1->getArrayCopy());
    }

    public function testYouCanFlipACollection()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7])->flip()->toArray();
        $this->assertEquals([1=>0, 2=>1, 3=>2, 6=>3, 7=>4], $s1);
    }

    public function testAppendingToACollectionReturnsANewCollection()
    {
        $s1 = Collection::create([1, 2, 3, 6, 7]);
        $s2 = $s1->append(8);
        $this->assertEquals([1,2,3,6,7,8], $s2->toArray());
    }
}
