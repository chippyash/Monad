# chippyash/Monad

## Quality Assurance

Certified for PHP 5.4+

[![Build Status](https://travis-ci.org/chippyash/Monad.svg?branch=master)](https://travis-ci.org/chippyash/Monad)
[![Coverage Status](https://coveralls.io/repos/chippyash/Monad/badge.svg?branch=master)](https://coveralls.io/r/chippyash/Monad?branch=master)
[![Code Climate](https://codeclimate.com/github/chippyash/Monad/badges/gpa.svg)](https://codeclimate.com/github/chippyash/Monad)

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

## Why?

PHP is coming under increasing attack from functional hybrid languages such as Scala.
The difference is the buzzword of `functional programming`. PHP can support this 
paradigm, and this library introduces some basic monadic types. Indeed, learning
functional programming practices can make solutions in PHP far more robust. 

Much of the power of monadic types comes through the use of the functional Match, 
For and Try language constructs.  PHP doesn't have these, and this library doesn't 
provide them. You can see these implemented in additional chippyash libraries.
 
Key to functional programming is the use of strict typing and elevating functions as
first class citizens within the language syntax. PHP5.4+ allows functions to be used as
a typed parameter (Closure). It also appears that PHP devs are coming to terms with
strict or hard types as the uptake of my [strong-type library](https://packagist.org/packages/chippyash/strong-type) testifies.

## How

### The Monadic interface

A Monad has three things (according to my understanding of it):

- a value (which may be no value at all, a simple type, an object or a function)
- method of getting its value, often referred to as return()
- a way of binding (or using) the value into some function, often refered to as  bind(), 
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
method is another (Monadic) class.  The original value is left alone

### The Monad Abstract class

Contains the Monad value holder and a `syntatic sugar` helper magic \__invoke() method that 
proxies to value() if no parameters supplied or bind() if a Closure and optional arguments
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

## Further documentation

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

We are still in pre production

<pre>
    "chippyash/monad": "dev-master"
</pre>

#### For development

Clone this repo, and then run Composer in local repo root to pull in dependencies

<pre>
    git clone git@github.com:chippyash/Monad.git Monad
    cd Monad
    composer update
</pre>

To run the tests:

<pre>
    cd Monad
    vendor/bin/phpunit -c test/phpunit.xml test/
</pre>

## License

This software library is released under the [GNU GPL V3 or later license](http://www.gnu.org/copyleft/gpl.html)

This software library is Copyright (c) 2015, Ashley Kitson, UK

This software library contains code items that are derived from other works: 

None of the contained code items breaks the overriding license, or vice versa,  as far as I can tell. 
So as long as you stick to GPL V3+ then you are safe. If at all unsure, please seek appropriate advice.

If the original copyright owners of the included code items object to this inclusion, please contact the author.

A commercial license is available for this software library, please contact the author. 

## Thanks

I didn't do this by myself. I'm deeply indebted to those that trod the path before me.
 
The following have done work on which this library is based:

[Sean Crystal](https://github.com/spiralout/Phonads)

[Anthony Ferrara](http://blog.ircmaxell.com/2013/07/taking-monads-to-oop-php.html)

[Johannes Schmidt](https://github.com/schmittjoh/php-option)

## History

V0...  pre releases