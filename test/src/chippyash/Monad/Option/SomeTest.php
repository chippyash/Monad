<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Test;

use Monad\Option\None;
use Monad\Option\Some;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    public function testYouCanConstructASomeIfYouHaveAValueForIt()
    {
        $this->assertInstanceOf(Some::class, new Some('foo'));
    }

    public function testYouCannotConstructASomeWithNoValue()
    {
        try {
            new Some();
        } catch (\Exception $e) {
            //php < 7.1
            $this->assertInstanceOf("PHPUnit_Framework_Error_Warning", $e);
        } catch (\ArgumentCountError $e) {
            //php >= 7.1
            $this->assertInstanceOf("ArgumentCountError", $e);
        }
    }

    public function testYouCanGetAValueFromASome()
    {
        $this->assertEquals('foo', (new Some('foo'))->value());
    }

    public function testBindingOnASomeMayReturnASomeOrANone()
    {
        $this->assertInstanceOf(Some::class, (new Some('foo'))->bind(function($value){return $value;}));
        $this->assertInstanceOf(None::class, (new Some('foo'))->bind(function($value){return null;}));
    }

    public function testBindingOnASomeTakesAThirdNonetestValue()
    {
        $sut = new Some('foo');
        $this->assertInstanceOf(Some::class, $sut->bind(function($value){return true;}, [], false));
        $this->assertInstanceOf(None::class, $sut->bind(function($value){return false;}, [], false));
    }
}
