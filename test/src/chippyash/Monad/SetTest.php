<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Monad\Test;

use Monad\Set;

class SetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testCreatingAnEmptySetWithNoTypeHintWillThrowAnException()
    {
        new Set([]);
    }

    public function testPassingInUniqueValuesAtConstructionWillCreateASet()
    {
        $this->assertInstanceOf('Monad\Set', new Set(['a','b','c']));
    }

    public function testPassingInNonUniqueValuesAtConstructionWillCreateASetWithUniqueValues()
    {
        $sut = new Set(['a','b','c','a','b','c']);
        $this->assertEquals(['a','b','c'], $sut->toArray());
    }

    public function testYouCanCreateSetsOfObjects()
    {
        $a = new \stdClass();
        $a->val = 'a';
        $b = new \stdClass();
        $b->val = 'b';
        $c = new \stdClass();
        $c->val = 'c';

        $this->assertInstanceOf('Monad\Set', new Set([$a, $b, $c]));

        $d = new \stdClass();
        $d->val = 'a';
        $e = new \stdClass();
        $e->val = 'b';

        $sut = new Set([$a, $b, $c, $d, $e]);
        $this->assertInstanceOf('Monad\Set', $sut);
        $test = array_map(
            function ($v) {
                return $v->val;
            },
            $sut->toArray()
        );
        $this->assertEquals(['a','b','c'], $test);
    }

    public function testYouCanCreateSetsOfResources()
    {
        $a = opendir(__DIR__);
        $b = opendir(__DIR__);
        $sut = new Set([$a, $b]);
        $this->assertInstanceOf('Monad\Set', $sut);
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
        $this->assertEquals(['a','b','c'], $test);
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
}
