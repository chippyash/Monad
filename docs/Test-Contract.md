# Chippyash Monad

## Monad\test\Failure

*  Can construct if value is exception
*  Create creates a failure if value is exception
*  Create creates a failure if value is not an exception
*  Bind returns failure with same value
*  Calling pass will throw an exception
*  Calling is success will return false

## Monad\test\Success

*  You can construct a success if you have a value for it
*  You cannot construct a success with an exception
*  Binding a success with something that does not throw an exception will return success
*  Binding a success with something that throws an exception will return a failure
*  You can get a value from a success
*  Calling is success will return true

## Monad\Test\Collection

*  You can construct a collection with a non empty array
*  You cannot construct a collection with null values
*  You cannot construct a collection with an empty array and no type specified
*  You can construct an empty collection if you pass a type
*  When constructing a collection you must have same type values
*  Constructing a collection with dissimilar types will cause an exception
*  You can create a collection
*  You can bind a function to the entire collection and return a collection
*  You can bind a function to each member of the collection and return a collection
*  You can count the items in the collection
*  You can get an iterator for a collection
*  You cannot unset a collection member by default
*  You can unset a collection member if you set the mutable flag
*  You cannot set a collection member by default
*  You can set a collection member if mutable flag is set
*  You can get a collection member as an array offset
*  You can test if a collection member exists as an array offset
*  You can create a collection of collections
*  Flattening a collection of collections will return a collection
*  You can get the difference between two collections
*  You can chain diff methods to act on arbitrary numbers of collections
*  You can supply an optional comparator function to diff method
*  You can get the intersection of two collections by value
*  You can get the intersection of two collections by key
*  You can chain value intersect methods to act on arbitrary numbers of collections
*  You can chain key intersect methods to act on arbitrary numbers of collections
*  You can supply an optional comparator function to the value intersect method
*  You can supply an optional comparator function to the key intersect method
*  You can get the union of values of two collections
*  You can chain the union of values of two collections
*  You can get the union of keys of two collections
*  You can chain the union of keys of two collections
*  Performing a value union with dissimilar collections will throw an exception
*  Performing a key union with dissimilar collections will throw an exception
*  The head of a collection is its first member
*  The tail of a collection is all but its first member
*  You can filter a collection with a closure
*  You can reduce a collection to a single value with a closure
*  You can reference a collection as though it was an array
*  Value method proxies to collection get array copy method
*  You can flip a collection

## Monad\Test\FTry

*  Creating an f try with a non exception will return a success
*  Creating an f try with an exception will return a failure
*  The with method proxies to create
*  Get or else will return f try value if option is a success
*  Get or else will return else value if f try is a failure

## Monad\Test\Identity

*  You can create an identity statically
*  Creating an identity with an identity parameter will return the parameter
*  Creating an identity with a non identity parameter will return an identity containing the parameter as value
*  You can bind a function on an identity
*  Bind can take optional additional parameters
*  You can chain bind methods together
*  Binding on an identity with a closure value will evaluate the value
*  You can flatten an identity value to its base type

## Monad\Test\Match

*  Construction requires a value to match against
*  You can construct via static on factory method
*  You can match on native php types
*  Matching will return set by callable parameter if matched
*  Matching will return set by non callable parameter if matched
*  Failing to match will return new match object with same value as original
*  You can match on a class name
*  Failing to match on class name will return new match object with same value as original
*  You can chain match tests
*  You can chain match tests and bind a function on successful match
*  Binding a match will return a match
*  Match on any method will match anything
*  Match on any method can accept optional function and arguments
*  You can nest matches
*  You can test for equality

## Monad\Test\Monad

*  You can return a value when monad created with simple value
*  You can return a value when monad created with monadic value
*  You can use a closure for value
*  Flatten will return base type
*  You can bind a closure on a monad to create a new monad of the same type
*  Bind can take optional additional parameters
*  Magic invoke proxies to bind method if passed a closure
*  Magic invoke proxies to value method if passed no parameters
*  Calling magic invoke will throw exception if no method is executable
*  You cannot create an abstract monad statically

## Monad\Test\None

*  You can construct a none
*  You can construct a none with a parameter and it will still be none
*  Create will return a none
*  Binding a none returns a none
*  Calling get on a none throws a runtime exception

## Monad\Test\Some

*  You can construct a some if you have a value for it
*  You cannot construct a some with no value
*  You can get a value from a some
*  Binding on a some may return a some or a none
*  Binding on a some takes a third nonetest value

## Monad\Test\Option

*  You cannot construct an option directly
*  Creating with a value returns a some
*  Creating with no value or null returns a none
*  You can replace none test by calling create with additional parameter
*  Get or else will return option value if option is a some
*  Get or else will return else value if option is a none


Generated by [chippyash/testdox-converter](https://github.com/chippyash/Testdox-Converter)