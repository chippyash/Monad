<?php
/**
 * Monad
 *
 * @author    Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license   GPL V3+ See LICENSE.md
 */
namespace Monad\Test;

use Monad\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function testCreatingAnEmptySetWithNoTypeHintWillThrowAnException()
    {
        $this->expectException(\RuntimeException::class);
        new Set([]);
    }

    public function testPassingInUniqueValuesAtConstructionWillCreateASet()
    {
        $this->assertInstanceOf(Set::class, new Set(['a', 'b', 'c']));
    }

    public function testPassingInNonUniqueValuesAtConstructionWillCreateASetWithUniqueValues(
    )
    {
        $sut = new Set(['a', 'b', 'c', 'a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $sut->toArray());
    }

    public function testYouCanCreateSetsOfObjects()
    {
        $a = new \stdClass();
        $a->val = 'a';
        $b = new \stdClass();
        $b->val = 'b';
        $c = new \stdClass();
        $c->val = 'c';

        $this->assertInstanceOf(Set::class, new Set([$a, $b, $c]));

        $d = new \stdClass();
        $d->val = 'a';
        $e = new \stdClass();
        $e->val = 'b';

        $sut = new Set([$a, $b, $c, $d, $e]);
        $this->assertInstanceOf(Set::class, $sut);
        $test = array_map(
            function ($v) {
                return $v->val;
            },
            $sut->toArray()
        );
        $this->assertEquals(['a', 'b', 'c'], $test);
    }

    public function testYouCanCreateSetsOfResources()
    {
        $a = opendir(__DIR__);
        $b = opendir(__DIR__);
        $sut = new Set([$a, $b]);
        $this->assertInstanceOf(Set::class, $sut);
        closedir($a);
        closedir($b);
    }

    public function testValueIntersectionWillProduceASet()
    {
        $a = new \stdClass();
        $a->val = 'a';
        $b = new \stdClass();
        $b->val = 'b';
        $c = new \stdClass();
        $c->val = 'c';

        $setA = new Set([$a, $b]);
        $setB = new Set([$a, $c]);

        $test = array_map(
            function ($v) {
                return $v->val;
            },
            $setA->vIntersect($setB)->toArray()
        );
        $this->assertEquals(['a'], $test);
    }

    public function testValueUnionWillProduceASet()
    {
        $a = new \stdClass();
        $a->val = 'a';
        $b = new \stdClass();
        $b->val = 'b';
        $c = new \stdClass();
        $c->val = 'c';

        $setA = new Set([$a, $b]);
        $setB = new Set([$a, $c]);

        $test = array_map(
            function ($v) {
                return $v->val;
            },
            $setA->vUnion($setB)->toArray()
        );
        $this->assertEquals(['a', 'b', 'c'], $test);
    }

    public function testDiffWillProduceASet()
    {
        $a = new \stdClass();
        $a->val = 'a';
        $b = new \stdClass();
        $b->val = 'b';
        $c = new \stdClass();
        $c->val = 'c';

        $setA = new Set([$a, $b]);
        $setB = new Set([$a, $c]);

        $test = array_map(
            function ($v) {
                return $v->val;
            },
            $setA->diff($setB)->toArray()
        );

        $this->assertEquals(['b'], $test);
    }

    public function testYouCanBindAFunctionToTheEntireSetAndReturnASet()
    {
        $sut = Set::create([2, 3, 4, 5, 6]);
        //function returns a single value - converted to a collection
        $f = function ($c) {
            return $c[0];
        };
        $this->assertEquals([2], $sut->bind($f)->getArrayCopy());
        $this->assertInstanceOf('Monad\Set', $sut->bind($f));
    }

    public function testKintersectMethodIsNotSupportedForSets()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('kIntersect is not a supported method for Sets');
        $setA = Set::create([2, 3, 4, 5, 6]);
        $setB = Set::create([2, 3, 4, 5, 6]);
        $setA->kIntersect($setB);
    }

    public function testKunionMethodIsNotSupportedForSets()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('kUnion is not a supported method for Sets');
        $setA = Set::create([2, 3, 4, 5, 6]);
        $setB = Set::create([2, 3, 4, 5, 6]);
        $setA->kUnion($setB);
    }

    public function testKdiffMethodIsNotSupportedForSets()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('kDiff is not a supported method for Sets');
        $setA = Set::create([2, 3, 4, 5, 6]);
        $setB = Set::create([2, 3, 4, 5, 6]);
        $setA->kDiff($setB);
    }

}
