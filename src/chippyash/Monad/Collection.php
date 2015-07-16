<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad;
use Monad\FTry\Success;
use Monad\FTry\Failure;
use Monad\FTry;
use Monad\Option\None;
use Monad\Option\Some;


/**
 * Key value pair collection object
 */
class Collection extends \ArrayObject implements Monadic
{

    /**
     * The Type of the items in the collection
     *
     * @var string
     */
    protected $type;

    /**
     * Constructor
     *
     * If you do not specify $type, it will be inferred from the first item in the
     * value array.  If value is empty and type is not specified, will throw an exception
     *
     * @param array $value Associative array of data to set
     * @param string $type Specific type of data contained in the array
     */
    public function __construct(array $value = [], $type = null)
    {
        $setType = Match::on($type)
            ->string(function() use($type) {return $this->setType($type);})
            ->null(function() use ($value) {return $this->setTypeFromValue($value);});

        parent::__construct(
            Match::on($setType->value())
                ->Monad_FTry_Success(function() use ($value) {return $this->setValue($value);})
                ->Monad_FTry_Failure(function() use ($setType) {return $setType->value();})
                ->value()
                ->pass()
                ->value()
        );
    }

    /**
     * Monadic Interface
     *
     * @param array $value
     *
     * @return Monadic
     */
    public static function create($value)
    {
        return new static($value);
    }

    /**
     * Bind monad with function.  Function is in form f($value){}
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN)
     *
     * @param \Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return Collection
     */
    public function bind(\Closure $function, array $args = [])
    {
        $res = $this->callFunction($function, $this, $args);
        return $res instanceof Collection ? $res : new static(is_array($res)? $res :[$res]);
    }

    /**
     * Monadic Interface
     *
     * @param callable $function
     * @param array $args
     *
     * @return Collection
     */
    public function each(\Closure $function, array $args = [])
    {
        $result = [];
        foreach($this as $key=> $value) {
            $result[$key] = $this->callFunction($function, $value, $args);
        }

        return new static($result);
    }

    /**
     * Reduce the collection using closure to a single value
     *
     * @see array_reduce
     *
     * @param \Closure $function
     * @param mixed $initial optional initial value
     *
     * @return mixed
     */
    public function reduce(\Closure $function, $initial = null)
    {
        return \array_reduce($this->getArrayCopy(), $function, $initial);
    }

    /**
     * Filter collection using closure to return another Collection
     *
     * @see array_filter
     *
     * @param \Closure $function
     *
     * @return Collection
     */
    public function filter(\Closure $function)
    {
        return new static(\array_filter($this->getArrayCopy(), $function));
    }

    /**
     * Monadic Interface
     * Return this collection as value
     *
     * @return Collection
     */
    public function value()
    {
        return $this;
    }

    /**
     * Return value of Monad as a base type.
     * If value === \Closure, will evaluate the function and return it's value
     * If value === \Monadic, will recurse
     *
     * @return Collection
     */
    public function flatten()
    {
        $ret = [];
        foreach ($this as $key => $value)
        {
            $ret[$key] = Match::on($value)
                ->Closure(function($v){return $v();})
                ->Monad_Monadic(function($v){return $v->flatten();})
                ->any()
                ->flatten();
        }

        return new static($ret);
    }

    /**
     * Return collection as an array
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throw \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Cannot set on an immutable Collection');
    }

    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     *
     * @throw \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Cannot unset an immutable Collection value');
    }

    /**
     * Compares this collection against another collection and returns a new Collection
     * with the values in this collection that are not present in the other collection.
     * Note that keys are preserved
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * @param Collection $other
     * @param Closure Optional function to compare values
     *
     * @return Collection
     */
    public function diff(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            return new static(\array_diff($this->getArrayCopy(), $other->getArrayCopy()), $this->type);
        }

        return new static(\array_udiff($this->getArrayCopy(), $other->getArrayCopy(), $function), $this->type);
    }

    /**
     * Returns a Collection containing all the values of this Collection that are present
     * in the other Collection. Note that keys are preserved
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * @param Collection $other
     * @param callable $function Optional function to compare values
     *
     * @return Collection
     */
    public function intersect(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            return new static(\array_intersect($this->getArrayCopy(), $other->getArrayCopy()), $this->type);
        }

        return new static(\array_uintersect($this->getArrayCopy(), $other->getArrayCopy(), $function), $this->type);
    }

    /**
     * Return a Collection that is the union of the values of this Collection
     * and the other Collection. Note that keys may be discarded and new ones set
     *
     * @param Collection $other
     * @param int $sortOrder arrayUnique sort order. one of SORT_...
     *
     * @return Collection
     */
    public function vUnion(Collection $other, $sortOrder = SORT_REGULAR)
    {
        return new static(
            \array_unique(
                \array_merge($this->getArrayCopy(), $other->getArrayCopy())
                , $sortOrder)
            , $this->type
        );
    }

    /**
     * Return a Collection that is the union of the values of this Collection
     * and the other Collection using the keys for comparison
     *
     * @param Collection $other
     *
     * @return Collection
     */
    public function kUnion(Collection $other)
    {
        return new static($this->getArrayCopy() + $other->getArrayCopy(), $this->type);
    }

    /**
     * Return a Collection with the first element of this Collection as its only
     * member
     *
     * @return Collection
     */
    public function head()
    {
        return new static(array_slice($this->getArrayCopy(), 0, 1));
    }

    /**
     * Return a Collection with all but the first member of this Collection
     *
     * @return Collection
     */
    public function tail()
    {
        return new static(array_slice($this->getArrayCopy(), 1));
    }


    /**
     * @param string $type
     *
     * @return FTry
     */
    protected function setType($type)
    {
        return Match::on($type)
            ->string(function($type){
                $this->type = $type;
                return FTry::with($type);
            })
            ->any(function(){
                return FTry::with(function(){return new \RuntimeException('Type must be specified by string');});
            })
            ->value();
    }

    /**
     * @param array $value
     *
     * @return FTry
     */
    protected function setTypeFromValue(array $value)
    {
        //required to be defined as a var so it can be called in next statement
        $basicTest = function() use($value) {
            if (count($value) > 0) {
                return array_values($value)[0];
            }

            return null;
        };

        //@var Option
        //firstValue is used twice below
        $firstValue = Option::create($basicTest());

        //@var Option
        //NB - this separate declaration is not needed, but is provided only to
        // allow some separation between what can become a complex match pattern
        $type = Match::on($firstValue)
            ->Monad_Option_Some(
                function($option){
                    return Option::create(gettype($option->value()));
                }
            )
            ->Monad_Option_None(function(){return new None();})
            ->value();

        //@var Option
        //MatchLegalType requires to be defined separately as it is used twice
        //in the next statement
        $matchLegalType = FTry::with(
            Match::on($type)
                ->Monad_Option_None()
                ->Monad_Option_Some(
                    function($v) use($firstValue) {
                        Match::on($v->value())
                            ->test('object', function($v) use($firstValue) {$this->setType(get_class($firstValue->value())); return new Some($v);})
                            ->test('string', function($v) {$this->setType($v); return new Some($v);})
                            ->test('integer', function($v) {$this->setType($v); return new Some($v);})
                            ->test('double', function($v) {$this->setType($v); return new Some($v);})
                            ->test('boolean', function($v) {$this->setType($v); return new Some($v);})
                            ->test('resource', function($v) {$this->setType($v); return new Some($v);})
                            ->any(function($v){return new None();});
                    }
                )
                ->any(function($v){return new None();})
        );

        return FTry::with(function() use($matchLegalType) {return $matchLegalType->value();});
    }

    /**
     * @param array $value
     * @return FTry
     */
    protected function setValue(array $values)
    {
        foreach ($values as $key => $value) {
            if (($this->type !== gettype($value)) && (!$value instanceof $this->type)) {
                return new Failure(new \RuntimeException("Value {$key} is not a {$this->type}"));
            }
        }

        return new Success($values);
    }

    /**
     * Call function on value
     *
     * @param \Closure $function
     * @param mixed $value
     * @param array $args additional arguments to pass to function
     *
     * @return Monadic
     */
    protected function callFunction(\Closure $function, $value, array $args = [])
    {
        if ($value instanceof Monadic && !$value instanceof Collection) {
            return $value->bind($function, $args);
        }
        if ($value instanceof \Closure) {
            $val = $value();
        } else {
            $val = $value;
        }
        array_unshift($args, $val);
        return call_user_func_array($function, $args);
    }
}