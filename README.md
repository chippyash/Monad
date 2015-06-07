# chippyash/Monad

## Quality Assurance

Certified for PHP 5.4+

[![Build Status](https://travis-ci.org/chippyash/Monad.svg?branch=master)](https://travis-ci.org/chippyash/Monad)
[![Coverage Status](https://coveralls.io/repos/chippyash/Monad/badge.png)](https://coveralls.io/r/chippyash/Monad)
[![Code Climate](https://codeclimate.com/github/chippyash/Monad/badges/gpa.svg)](https://codeclimate.com/github/chippyash/Monad)

## What?

Provides a Monadic type

According to my mentor, Monads are either difficult to explain or difficult to code, 
i.e. you can say `how` or `what` but not at the same time. Not sure I agree, but if
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
The threat is real, with the ability of such languages to implement web applications
using the `play` or `activator` framework.  The difference is the new buzzword of
`functional programming`. PHP can support this paradigm, and this library introduces
some basic monadic types.

Much of the power of monadic types comes through the use of the functional Match, 
For and Try language constructs.  PHP doesn't have these, and this library doesn't 
provide them. You can see these implemented in additional chippyash libraries.
 
Key to functional programming is the use of strict typing and elevating functions as
first class citizens within the language syntax. PHP5.4+ allows functions to be used as
a typed parameter (Closure). It also appears that PHP devs are coming to terms with
strict or hard types as the uptake of my [strong-type library](https://packagist.org/packages/chippyash/strong-type) testifies.

## How

@todo

## Further documentation

[Test Contract](https://github.com/chippyash/Monad/blob/master/docs/Test-Contract.md) in the docs directory.

### UML

@todo

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
    "chippyash/monad": "~1.0.0"
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





