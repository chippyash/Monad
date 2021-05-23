<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Identity;
use Monad\FMatch;
use Monad\Option;
use Monad\Option\Some;
use Monad\Option\None;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class FMatchTest extends TestCase
{
    public function testConstructionRequiresAValueToFMatchAgainst()
    {
        $this->assertInstanceOf(FMatch::class, new FMatch('foo'));
    }

    public function testYouCanConstructViaStaticOnFactoryMethod()
    {
        $this->assertInstanceOf(FMatch::class, FMatch::on('foo'));
    }

    public function testYouCanFMatchOnNativePhpTypes()
    {
        $this->assertEquals('foo', FMatch::on('foo')->string()->value());
        $this->assertEquals(1, FMatch::on(1)->int()->value());
        $this->assertEquals(1, FMatch::on(1)->integer()->value());
        $this->assertEquals(1.0, FMatch::on(1.0)->float()->value());
        $this->assertEquals(1.0, FMatch::on(1.0)->double()->value());
        $this->assertNull(FMatch::on(null)->null()->value());
        $this->assertEquals([], FMatch::on([])->array()->value());
        $this->assertTrue(FMatch::on(true)->bool()->value());
        $this->assertTrue(FMatch::on(true)->boolean()->value());
        $this->assertInstanceOf(\Closure::class, FMatch::on(function(){})->callable()->value());
        $this->assertInstanceOf(\Closure::class, FMatch::on(function(){})->function()->value());
        $this->assertInstanceOf(\Closure::class, FMatch::on(function(){})->closure()->value());
        $this->assertTrue(is_object(FMatch::on(new \stdClass())->object()->value()));
        $this->assertEquals(123, FMatch::on(123)->scalar()->value());
        $this->assertEquals(123, FMatch::on('123')->numeric()->value());
        $this->assertEquals(123, FMatch::on(123)->numeric()->value());

        $fileRoot = vfsStream::setup();
        $this->assertEquals('vfs://root', FMatch::on($fileRoot->url())->dir()->value());
        $this->assertEquals('vfs://root', FMatch::on($fileRoot->url())->directory()->value());

        $fileRoot->addChild(new vfsStreamFile('foo'));
        $this->assertEquals('vfs://root/foo', FMatch::on($fileRoot->url() . '/foo')->file()->value());

        $fh = fopen($fileRoot->url() . '/foo', 'r');
        $this->assertTrue(is_resource(FMatch::on($fh)->resource()->value()));
        fclose($fh);
    }

    public function testFMatchingWillReturnSetByCallableParameterIfFMatched()
    {
        $this->assertEquals('foobarfoo', FMatch::on('foobar')->string(function($val){return $val . 'foo';})->value());
    }

    public function testFMatchingWillReturnSetByNonCallableParameterIfFMatched()
    {
        $this->assertEquals('foobarfoo', FMatch::on('foobar')->string('foobarfoo')->value());
    }

    public function testFailingToFMatchWillReturnNewFMatchObjectWithSameValueAsOriginal()
    {
        $this->assertEquals(true, FMatch::on(true)->string()->value());
    }

    public function testYouCanFMatchOnAClassName()
    {
        $this->assertInstanceOf('StdClass', FMatch::on(new \StdClass)->StdClass()->value());
        $this->assertInstanceOf('Monad\FMatch', FMatch::on(new FMatch('foo'))->Monad_FMatch()->value());
    }

    public function testFailingToFMatchOnClassNameWillReturnNewFMatchObjectWithSameValueAsOriginal()
    {
        $val = new Identity('foo');
        $test = FMatch::on($val)->StdClass();
        $this->assertInstanceOf(FMatch::class, $test);
        $this->assertInstanceOf(Identity::class, $test->value());
        $this->assertEquals('foo', $test->flatten());
    }

    public function testYouCanChainFMatchTests()
    {
        $test = FMatch::on(true)
            ->string()
            ->int()
            ->bool()
            ->value();

        $this->assertTrue($test);
    }

    public function testYouCanChainFMatchTestsAndBindAFunctionOnSuccessfulFMatch()
    {
        $test = FMatch::on(true)
            ->string()
            ->int()
            ->bool(function(){return 'foo';})
            ->value();

        $this->assertEquals('foo', $test);
    }

    public function testBindingAFMatchWillReturnAFMatch()
    {
        $test = FMatch::on('foo')->bind(function($v){return $v . 'bar';});
        $this->assertInstanceOf(FMatch::class, $test);
        $this->assertEquals('foobar', $test->value());
    }

    /**
     * @dataProvider anyFMatchData
     * @param $value
     */
    public function testFMatchOnAnyMethodWillFMatchAnything($value)
    {
        $this->assertEquals(
            $value,
            FMatch::on($value)
                ->any()
                ->value()
        );
    }

    /**
     * @dataProvider anyFMatchData
     * @param $value
     */
    public function testFMatchOnAnyMethodCanAcceptOptionalFunctionAndArguments($value)
    {
        $this->assertEquals(
            'bar',
            FMatch::on($value)
                ->any(
                    function($v, $z){return $z;},
                    ['bar']
                )
            ->value()
        );
    }

    public function anyFMatchData()
    {
        date_default_timezone_set('UTC');
        return [
            [2],
            ['foo'],
            [1.13],
            [new \DateTime()],
            [new \StdClass()],
            [true],
            [false]
        ];
    }

    public function testYouCanNestFMatches()
    {
        $this->assertEquals('foo', $this->nestedFMatcher('foo')->value());
        $this->assertEquals('bar', $this->nestedFMatcher(Option::create('bar'))->value());
        try {
            $this->nestedFMatcher(Option::create());
            $this->fail('Expected an Exception but got none');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals('foobar', $this->nestedFMatcher(Identity::create('foo'))->value());
        //expecting match on any() as integer won't be matched
        $this->assertEquals('any', $this->nestedFMatcher(2)->value());
    }

    protected function nestedFMatcher($initialValue)
    {
        return FMatch::on($initialValue)
            ->string('foo')
            ->Monad_Option(
                function ($v) {
                    return FMatch::on($v)
                        ->Monad_Option_Some(function ($v) {
                            return $v->value();
                        })
                        ->Monad_Option_None(function () {
                            throw new \Exception();
                        })
                        ->value();
                    }
            )
            ->Monad_Identity(
                function ($v) {
                    return $v->value() . 'bar';
                }
            )
            ->any(
                function() {
                    return 'any';
                }
            );
    }

    public function testYouCanTestForEquality()
    {
        $test = FMatch::on('foo')
            ->test('foo')
            ->value();
        $this->assertEquals('foo', $test);

        $test = FMatch::on('foo')
            ->test('bar')
            ->value();
        $this->assertEquals('foo', $test);

        $test = FMatch::on('foo')
            ->test('foo', function($v){return new Some($v);})
            ->flatten();
        $this->assertEquals('foo', $test);

        $test = FMatch::on('bar')
            ->test('foo', function($v){return new Some($v);})
            ->any(function(){return new None();})
            ->value();
        $this->assertInstanceOf('Monad\Option\None', $test);

    }
}
