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
class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, Monadic
{
    use ReturnValueAble;
    use CallFunctionAble;

    /** 
     * @var array Data associated with the object. 
     */
    protected $value;

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

        Match::on($setType->value())
            ->Monad_FTry_Success(function() use ($value) {$this->setValue($value);})
            ->Monad_FTry_Failure(function() use ($setType) {$setType->value()->pass();});
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
        return new self($value);
    }


    /**
     * Monadic Interface
     *
     * @param callable $function
     * @param array $args
     *
     * @return Collection
     */
    public function bind(\Closure $function, array $args = [])
    {
        $result = [];
        foreach($this->value as $key=> $value) {
            $result[$key] = $this->callFunction($function, $value, $args);
        }

        return new self($result);
    }

    /**
     * Return value of Monad as a base type.
     * If value === \Closure, will evaluate the function and return it's value
     * If value === \Monadic, will recurse
     *
     * @return mixed
     */
    public function flatten()
    {
        $ret = [];
        foreach ($this->value as $key => $value)
        {
            $ret[$key] = Match::on($value)
                ->Closure(function($v){return $v();})
                ->Monad_Monadic(function($v){return $v->flatten();})
                ->any()
                ->flatten();
        }

        return $ret;
    }

    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->value[$offset]);
    }

    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->value[$offset]) ? $this->value[$offset] : null;
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
     * IteratorAggregate Interface
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->value);
    }

    /**
     * Countable Interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->value);
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
            return isset($value[0]) && !is_null($value[0]) ? $value[0] : null;
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

        $this->value = $values;

        return new Success($values);
    }
}