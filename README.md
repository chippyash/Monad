# chippyash/Monad

## Quality Assurance

Certified for PHP 5.4+

[![Build Status](https://travis-ci.org/chippyash/Monad.svg?branch=master)](https://travis-ci.org/chippyash/Monad)
[![Coverage Status](https://coveralls.io/repos/chippyash/Monad/badge.svg?branch=master)](https://coveralls.io/r/chippyash/Monad?branch=master)
[![Code Climate](https://codeclimate.com/github/chippyash/Monad/badges/gpa.svg)](https://codeclimate.com/github/chippyash/Monad)

The above badges represent the current development branch.  As a rule, I don't push
 to GitHub unless tests, coverage and usability are acceptable.  This may not be
 true for short periods of time; on holiday, need code for some other downstream
 project etc.  If you need stable code, use a tagged version. Read 'Further Documentation'
 and 'Installation'.
 
## What?

Provides a Monadic type

According to my mentor, Monads are either difficult to explain or difficult to code, 
i.e. you can say `how` or `what` but not at the same time. If
you need further illumination, start with [wikipedia](http://en.wikipedia.org/wiki/Monad_\(functional_programming\))

### Types supported

* Monadic Interface
* Abstract Monad
* Identity Monad
* Option Monad
    * Some
    * None
* FTry Monad
    * Success
    * Failure
* Match Monad
* Collection Monad

## Why?

PHP is coming under increasing attack from functional hybrid languages such as Scala.
The difference is the buzzword of `functional programming`. PHP can support this 
paradigm, and this library introduces some basic monadic types. Indeed, learning
functional programming practices can make solutions in PHP far more robust. 

Much of the power of monadic types comes through the use of the functional Match, 
Try and For Comprehension language constructs.  PHP doesn't have these. This library provides:

- Match
- FTry
- FFor (TBC)
 
Key to functional programming is the use of strict typing and elevating functions as
first class citizens within the language syntax. PHP5.4+ allows functions to be used as
a typed parameter (Closure). It also appears that PHP devs are coming to terms with
strict or hard types as the uptake of my [strong-type library](https://packagist.org/packages/chippyash/strong-type) testifies.

## How

### The Monadic interface

A Monad has three things (according to my understanding of it):

- a value (which may be no value at all, a simple type, an object or a function)
- method of getting its value, often referred to as return()
- a way of binding (or using) the value into some function, often referred to as  bind(), 
the return value of which is another Monad, often but not always of the same type as 
the donor Monad. (Rarely, it could be another class type.)

The Monadic Interface supplied here defines

- bind(\Closure $function, array $args = []):Monadic
  - The function signature should contain at least one parameter to receive the value
   of the Monad. e.g. `function($val){return $val * 2;}` If you are using additional
   arguments in the $args array, you'll need to add them to the parameter list e.g.
<pre>
$ret = $m->bind(function($val, $mult){return $val * $mult;}, [4]);
</pre>
  Bear in mind that you can use the `use` clause as normal when defining your function 
    to expose external parameter values
    
    
- value():mixed - the return() method as `return` is a reserved word in PHP

Additionally, two helper methods are defined for the interface

- flatten():mixed - the monadic value `flattened` to a PHP native type or non Monadic object
- static create(mixed $value):Monadic A factory method to create an instance of the concrete descendant Monad

Monads have an immutable value, that is to say, the result of the bind()
method is another (Monadic) class.  The original value is left alone.

### The Monad Abstract class

Contains the Monad value holder and a `syntatic sugar` helper magic \__invoke() method that 
proxies to value() if no parameters supplied or bind() if a Closure (with/without optional arguments)
are supplied.

Neither the Monadic interface or the abstract Monad class define how to set a value on
construction of a concrete Monad.  It usually makes sense to set the Monad's value on construction.
Therefore in most circumstances you would create concrete Monad classes with some
form of constructor.
 
### Concrete Monad classes supplied

#### Identity
The simplest type of Monad

<pre>
use Monad\Identity;

$id = new Identity('foo');
//or
$id = Identity::create('foo');

$fConcat = function($value, $fudge){return $value . $fudge};
$concat = $id->bind($fConcat, ['bar])
             ->bind($fConcat, ['baz']);

echo $concat->value();      //'foobarbaz'
echo $id->value();          //'foo'
</pre>

#### Option

An Option is a polymorphic `Maybe Monad` that can exist in one of two states:

- Some - an option with a value
- None - an option with no value

As PHP does not have the language construct to create a polymorphic object by construction,
you'll need to use the Option::create() static method.  You can however use Option as 
a type hint for other class methods and returns

<pre>
use Monad\Option;
use Monad\Option\Some;
use Monad\Option\None;

/**
 * @param Option $opt
 * @return Option
 */
function doSomethingWithAnOption(Option $opt) {
    if ($opt instanceof None) {
        return $opt;
    }
    
    //must be a Some
    return $opt(doMyOtherThing()); //use magic invoke to bind

}

$someOption = Option::create('foo);
$noneOption = Option::create();

$one = doSomethingWithAnOption($someOption);
$two = doSomethingWithAnOption($noneOption);
</pre>

Under normal circumstances, Option uses the `null` value to determine whether or not
 to create a Some or a None; that is, the value passed into create() is tested against
 `null`. If it === null, then a None is created, else a Some.  You can provide an
 alternative test value as a second parameter to create()
 
<pre>
$mySome = Option:;create(true, false);
$myNone = Option::create(false, false);
</pre>

Once a None, always a None. No amount of binding will return anything other than a None.  
On the other hand, a Some can become a None through binding, (or rather the result of 
the bind() as of course the original Some remains immutable.)  To assist in this,
Some->bind() can take an optional third parameter, which is the value to test against
for None (i.e. like the optional second parameter to Option::create() )

You should also note that calling ->value() on a None will generate a RuntimeException
because of course, a None does not have a value!

##### Other methods supported

* getOrElse(mixed:elseValue) If the Option is a Some, return the Option->value() else
    return the elseValue
    
#### FTry

An FTry is a polymorphic `Try Monad` that can exist in one of two states:

- Success - an FTry with a value
- Failure - an FTry with a PHP Exception as a value

`Try` is a reserved word in PHP, so I have called this class FTry to mean `Functional Try`.

As PHP does not have the language construct to create a polymorphic object by construction,
you'll need to use the FTry::with() (or FTry::create()) static method.  You can however 
use FTry as a type hint for other class methods and returns

FTry::on(value) will catch any Exception incurred in processing the value, and return
a Success or Failure class appropriately.  This makes it ideal for the simple case
of wrapping a PHP transaction in a Try - Catch block:
 
<pre>
use Monad\FTry;
use Monad\Match;

Match::on(FTry::with($myFunction($initialValue())))
    ->Monad_FTry_Success(function ($v) {doSomethingGood($v);})
    ->Monad_FTry_Failure(
        function (\Exception $e) {
            echo "Exception: " . $e->getMessage(); 
        }
    );
</pre>

A fairly simplistic example, and one where you might question its value, as it could have
 been written as easily using conventional PHP.  But: A Success or Failure is still 
 a Monad, and so you can still bind (map) onto the resultant class, flatten it etc.
 
Like Option, FTry also supports the `getOrElse(mixed:elseValue)` method allowing for implementing
default behaviours:

<pre>
echo FTry::with(myComplexPrintableTransaction())
    ->getOrElse('Sorry - that failed');
</pre>

For completeness, FTry also supports `isSuccess()`:

<pre>
echo 'The colour is' . FTry::with(myTest())->isSuccess() ? 'blue' : 'red';
</pre>

Once a Failure, always a Failure.  However, A Success can yield either a Success
or a Failure as a result of binding.

If you really want to throw the exception contained in a Failure use the `pass()` method

<pre>
$try = FTry::with($myFunction());
if (!$try->isSuccess()) $try->pass();
</pre>

#### Match

The Match Monad allows you to carry out type pattern matching to create powerful and 
dynamic functional equivalents of `case statements`.

The basic syntax is

<pre>
use Monad\Match;

$result = Match::on($initialValue)
            ->test()
            ->test()
            ->value();
</pre>

where test() can be the name of a native PHP type or the name of a class, e.g.:

<pre>
$result = Match::on($initialValue)
            ->string()
            ->Monad_Option()
            ->Monad_Identity()
            ->value()
</pre>

You can use the Match::any() method to catch anything not matched by a specific matcher:

<pre>
$result = Match::on($initialValue)
            ->string()
            ->int()
            ->any()
            ->value();
</pre>

You can provide a concrete value as a parameter to each test, or a function. e.g.

<pre>
$result = Match::on($initialValue)
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
              ->any(function(){return 'any';})
              ->value();
</pre>

You can find this being tested in MatchTest::testYouCanNestMatches()

##### Supported native type matches

- string
- integer|int|long
- float|double|real
- null
- array
- bool|boolean
- callable|function|closure
- file
- dir|directory
- object
- scalar
- numeric
- resource

##### Supported class matching

Use the fully namespaced name of the class to match, substituting the backslash \\
with an underscore e.g. to test for `Monad\Option` use `Monad_Option`

#### Collection

The Monad Collection provides a structured array that behaves as a Monad.  

Very important to note however is that unlike a PHP array, 
the Collection is type specific, i.e. you specify Collection specifically or by default
as the first member of its construction array.  

<pre>
use Monad\Collection;

$c = Collection::create([1,2,3,4]);
//or
$c = Collection::create([1,2,3,4], 'integer');

//to create an empty collection, you must specify type
$c = Collection::create([], 'integer');
$c = Collection::create([], 'Monad\Option');
</pre>

You can get and test a Collection:
 
<pre>
$c = Collection::create([1,2,3,4]);
$v = $c[2] // == 3

if (!isset($c[6]) { 
... 
}
</pre>
 
Although the Collection implements
 the ArrayAccess interface, trying to set or unset a value `$mCollection[0] = 'foo'` or
 `unset($mCollection[0])` *will* throw an exception, as Collections are immutable.
 
As usual, this is not really a problem, as you can bind() on a Collection to return
  another Collection, (which can contain values of a different type.)  Wherever possible, 
  I've expressed the Collection implementation in terms of Match statements, not only 
  because it usually means tighter code, but as something that you can look at 
  (and criticise hopefully!) by example.

You can get the difference of two collections:

<pre>
$s1 = Collection::create([1, 2, 3, 6, 7]);
$s2 = Collection::create([6,7]);
$s3 = $s1->diff($s2);
</pre>

And the intersection:

<pre>
$s1 = Collection::create([1, 2, 3, 6, 7]);
$s2 = Collection::create([6,7]);
$s3 = $s1->intersect($s2);
</pre>

Both `diff` and `intersect` can take a second optional Closure parameter which is used
as the comparator method.

You can get the union of two collections, either by value or key:

<pre>
$s1 = Collection::create([1, 2, 3, 6, 7]);
$s2 = Collection::create([3, 6, 7, 8]);
$valueUnion = $s1->vUnion($s2);
$keyUnion =  $s1->kUnion($s2);
</pre>

You can get the head and the tail of a collection:

<pre>
$s1 = Collection::create([1, 2, 3, 6, 7]);
echo $s1->head()->value()[0] // 1
echo $s1->tail()->value()[0] // 2
echo $s1->tail()->value()[3] // 7
</pre>

I chose Collection as the name as it doesn't clash with `list` which is a PHP reserved name.
In essence, Collection will to all intents and purposes be a List, but for die hard PHPers
still behave as an array.

A secondary design consideration, is that you should be able to use Collection 
oblivious of that fact that it is a Monad, except that it is type specific.
 
## Further documentation

Please note that what you are seeing of this documentation displayed on Github is
always the latest dev-master. The features it describes may not be in a released version
 yet. Please check the documentation of the version you Compose in, or download.

[Test Contract](https://github.com/chippyash/Monad/blob/master/docs/Test-Contract.md) in the docs directory.

### UML

![class diagram](https://github.com/chippyash/Monad/blob/master/docs/monad-classes.png)

## Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

Or - raise an issue ticket.

## Where?

The library is hosted at [Github](https://github.com/chippyash/Monad). It is
available at [Packagist.org](https://packagist.org/packages/chippyash/monad)

### Installation

Install [Composer](https://getcomposer.org/)

#### For production

<pre>
    "chippyash/monad": "~1.2.0"
</pre>

Or to use the latest, possibly unstable version:

<pre>
    "chippyash/monad": "dev-master"
</pre>


#### For development

Clone this repo, and then run Composer in local repo root to pull in dependencies

<pre>
    git clone git@github.com:chippyash/Monad.git Monad
    cd Monad
    composer install
</pre>

To run the tests:

<pre>
    cd Monad
    vendor/bin/phpunit -c test/phpunit.xml test/
</pre>

##### Debugging

Because PHP doesn't really support functional programming at it's core level, debugging
  using XDebug etc becomes a nested mess. Some things I've found helpful:

- isolate your tests, at least at the initial stage. If you get a problem, create a test
that does one thing - the thing you are trying to debug. Use that as your start point.

- be mindful of value() and flatten(), the former gets the immediate Monad value, the
latter gives you a PHP fundamental.

- when constructing Matches, ensure the value contained in the Match conforms to the
type you are expecting.  Remember, Match returns a Match with a value. And yes, I've
tripped up on this myself.

- keep running the other tests. Seems simple, but in the headlong pursuit of your
single objective, it's easy to forget that the library is interdependent (and will 
 become increasingly so as we are able to wrap new functionality back into the original
 code. e.g. Collection is dependent on Match: when FFor is implemented, Match will change.)
 Run the whole test suite on a regular basis. That way you catch anything that has broken
 upstream functionality.  This library will be complete when it, as far as possible,
 expresses itself in terms of itself!
 
- the tests that are in place are there for a good reason: open an issue if you think
they are wrong headed, misguided etc

## License

This software library is released under the [GNU GPL V3 or later license](http://www.gnu.org/copyleft/gpl.html)

This software library is Copyright (c) 2015, Ashley Kitson, UK

This software library contains code items that are derived from other works: 

None of the contained code items breaks the overriding license, or vice versa,  as far as I can tell. 
So as long as you stick to GPL V3+ then you are safe. If at all unsure, please seek appropriate advice.

If the original copyright owners of the derived code items object to this inclusion, please contact the author.

A commercial license is available for this software library, please contact the author. 
It is normally free to deserving causes, but gets you around the limitation of the GPL
license, which does not allow unrestricted inclusion of this code in commercial works.

## Thanks

I didn't do this by myself. I'm deeply indebted to those that trod the path before me.
 
The following have done work on which this library is based:

[Sean Crystal](https://github.com/spiralout/Phonads)

[Anthony Ferrara](http://blog.ircmaxell.com/2013/07/taking-monads-to-oop-php.html)

[Johannes Schmidt](https://github.com/schmittjoh/php-option)

## History

V1.0.0 Initial Release

V1.1.0 Added FTry

V1.2.0 Added Collection