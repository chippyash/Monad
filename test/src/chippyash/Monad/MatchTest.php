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
use Monad\Match;
use Monad\Option;
use Monad\Option\Some;
use Monad\Option\None;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class MatchTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionRequiresAValueToMatchAgainst()
    {
        $this->assertInstanceOf('Monad\Match', new Match('foo'));
    }

    public function testYouCanConstructViaStaticOnFactoryMethod()
    {
        $this->assertInstanceOf('Monad\Match', Match::on('foo'));
    }

    public function testYouCanMatchOnNativePhpTypes()
    {
        $this->assertEquals('foo', Match::on('foo')->string()->value());
        $this->assertEquals(1, Match::on(1)->int()->value());
        $this->assertEquals(1, Match::on(1)->integer()->value());
        $this->assertEquals(1.0, Match::on(1.0)->float()->value());
        $this->assertEquals(1.0, Match::on(1.0)->double()->value());
        $this->assertNull(Match::on(null)->null()->value());
        $this->assertEquals([], Match::on([])->array()->value());
        $this->assertTrue(Match::on(true)->bool()->value());
        $this->assertTrue(Match::on(true)->boolean()->value());
        $this->assertInstanceOf('Closure', Match::on(function(){})->callable()->value());
        $this->assertInstanceOf('Closure', Match::on(function(){})->function()->value());
        $this->assertInstanceOf('Closure', Match::on(function(){})->closure()->value());
        $this->assertTrue(is_object(Match::on(new \stdClass())->object()->value()));
        $this->assertEquals(123, Match::on(123)->scalar()->value());
        $this->assertEquals(123, Match::on('123')->numeric()->value());
        $this->assertEquals(123, Match::on(123)->numeric()->value());

        $fileRoot = vfsStream::setup();
        $this->assertEquals('vfs://root', Match::on($fileRoot->url())->dir()->value());
        $this->assertEquals('vfs://root', Match::on($fileRoot->url())->directory()->value());

        $fileRoot->addChild(new vfsStreamFile('foo'));
        $this->assertEquals('vfs://root/foo', Match::on($fileRoot->url() . '/foo')->file()->value());

        $fh = fopen($fileRoot->url() . '/foo', 'r');
        $this->assertTrue(is_resource(Match::on($fh)->resource()->value()));
        fclose($fh);
    }

    public function testMatchingWillReturnSetByCallableParameterIfMatched()
    {
        $this->assertEquals('foobarfoo', Match::on('foobar')->string(function($val){return $val . 'foo';})->value());
    }

    public function testMatchingWillReturnSetByNonCallableParameterIfMatched()
    {
        $this->assertEquals('foobarfoo', Match::on('foobar')->string('foobarfoo')->value());
    }

    public function testFailingToMatchWillReturnNewMatchObjectWithSameValueAsOriginal()
    {
        $this->assertEquals(true, Match::on(true)->string()->value());
    }

    public function testYouCanMatchOnAClassName()
    {
        $this->assertInstanceOf('StdClass', Match::on(new \StdClass)->StdClass()->value());
        $this->assertInstanceOf('Monad\Match', Match::on(new Match('foo'))->Monad_Match()->value());
    }

    public function testFailingToMatchOnClassNameWillReturnNewMatchObjectWithSameValueAsOriginal()
    {
        $val = new Identity('foo');
        $test = Match::on($val)->StdClass();
        $this->assertInstanceOf('Monad\Match', $test);
        $this->assertInstanceOf('Monad\Identity', $test->value());
        $this->assertEquals('foo', $test->flatten());
    }

    public function testYouCanChainMatchTests()
    {
        $test = Match::on(true)
            ->string()
            ->int()
            ->bool()
            ->value();

        $this->assertTrue($test);
    }

    public function testYouCanChainMatchTestsAndBindAFunctionOnSuccessfulMatch()
    {
        $test = Match::on(true)
            ->string()
            ->int()
            ->bool(function(){return 'foo';})
            ->value();

        $this->assertEquals('foo', $test);
    }

    public function testBindingAMatchWillReturnAMatch()
    {
        $test = Match::on('foo')->bind(function($v){return $v . 'bar';});
        $this->assertInstanceOf('Monad\Match', $test);
        $this->assertEquals('foobar', $test->value());
    }

    /**
     * @dataProvider anyMatchData
     * @param $value
     */
    public function testMatchOnAnyMethodWillMatchAnything($value)
    {
        $this->assertEquals(
            $value,
            Match::on($value)
                ->any()
                ->value()
        );
    }

    /**
     * @dataProvider anyMatchData
     * @param $value
     */
    public function testMatchOnAnyMethodCanAcceptOptionalFunctionAndArguments($value)
    {
        $this->assertEquals(
            'bar',
            Match::on($value)
                ->any(
                    function($v, $z){return $z;},
                    ['bar']
                )
            ->value()
        );
    }

    public function anyMatchData()
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

    public function testYouCanNestMatches()
    {
        $this->assertEquals('foo', $this->nestedMatcher('foo')->value());
        $this->assertEquals('bar', $this->nestedMatcher(Option::create('bar'))->value());
        try {
            $this->nestedMatcher(Option::create());
            $this->fail('Expected an Exception but got none');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals('foobar', $this->nestedMatcher(Identity::create('foo'))->value());
        //expecting match on any() as integer won't be matched
        $this->assertEquals('any', $this->nestedMatcher(2)->value());
    }

    protected function nestedMatcher($initialValue)
    {
        return Match::on($initialValue)
            ->string('foo')
            ->Monad_Option(
                function ($v) {
                    return Match::on($v)
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
        $test = Match::on('foo')
            ->test('foo')
            ->value();
        $this->assertEquals('foo', $test);

        $test = Match::on('foo')
            ->test('bar')
            ->value();
        $this->assertEquals('foo', $test);

        $test = Match::on('foo')
            ->test('foo', function($v){return new Some($v);})
            ->flatten();
        $this->assertEquals('foo', $test);

        $test = Match::on('bar')
            ->test('foo', function($v){return new Some($v);})
            ->any(function(){return new None();})
            ->value();
        $this->assertInstanceOf('Monad\Option\None', $test);

    }
}
