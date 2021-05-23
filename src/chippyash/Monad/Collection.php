<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
declare(strict_types=1);
namespace Monad;

use ArrayObject;
use Monad\FTry\Success;
use Monad\FTry\Failure;
use Monad\Option\None;
use Monad\Option\Some;

/**
 * Key value pair collection object
 */
class Collection extends ArrayObject implements Monadic
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
        /** @noinspection PhpUndefinedMethodInspection */
        $setType = FMatch::on($type)
            ->string(function () use ($type) {
                return $this->setType($type);
            })
            ->null(function () use ($value) {
                return $this->setTypeFromValue($value);

            });

        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(
            FMatch::on($setType->value())
                ->Monad_FTry_Success(function () use ($value) {
                    return $this->setValue($value);
                })
                ->Monad_FTry_Failure(function () use ($setType) {
                    return $setType->value();
                })
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
     * @return Collection
     */
    public static function create($value): Collection
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
        
        return ($res instanceof Collection ? $res : new static(is_array($res)? $res :[$res]));
    }

    /**
     * For each item in the collection apply the function and return a new collection
     *
     * @param callable|\Closure $function
     * @param array $args
     *
     * @return Collection
     */
    public function each(\Closure $function, array $args = []): Collection
    {
        $content = $this->getArrayCopy();
        
        return new static(
            \array_combine(
                \array_keys($content),
                \array_map(
                    function ($value) use ($function, $args) {
                        return $this->callFunction($function, $value, $args);
                    },
                    \array_values($content)
                )
            )
        );
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
    public function filter(\Closure $function): Collection
    {
        return new static(\array_filter($this->getArrayCopy(), $function));
    }

    /**
     * Flip keys and values.
     * Returns new Collection
     *
     * @return Collection
     */
    public function flip(): Collection
    {
        return new static(\array_flip($this->getArrayCopy()));
    }

    /**
     * Monadic Interface
     * Return this collection as value
     *
     * @return Collection
     */
    public function value(): Collection
    {
        return $this;
    }

    /**
     * Return value of Monad as a base type.
     *
     * If value === \Closure, will evaluate the function and return it's value
     * If value === \Monadic, will recurse
     *
     * @return Collection
     */
    public function flatten(): Collection
    {
        $ret = [];
        foreach ($this as $key => $value) {
            $ret[$key] = FMatch::on($value)
                ->Closure(function ($v) {
                    return $v();
                })
                ->Monad_Monadic(function ($v) {
                    return $v->flatten();
                })
                ->any()
                ->flatten();
        }

        return new static($ret);
    }

    /**
     * Return collection as an array
     * @return array
     */
    public function toArray(): array
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
     * @deprecated Use vDiff
     *
     * @param Collection $other
     * @param \Closure $function optional function to compare values
     *
     * @return Collection
     */
    public function diff(Collection $other, \Closure $function = null): Collection
    {
        return $this->vDiff($other, $function);
    }

    /**
     * Compares this collection against another collection using its values for
     * comparison and returns a new Collection with the values in this collection
     * that are not present in the other collection.
     *
     * Note that keys are preserved
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * @param Collection $other
     * @param \Closure $function optional function to compare values
     *
     * @return Collection
     */
    public function vDiff(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            return new static(\array_diff($this->getArrayCopy(), $other->getArrayCopy()), $this->type);
        }

        return new static(\array_udiff($this->getArrayCopy(), $other->getArrayCopy(), $function), $this->type);
    }

    /**
     * Compares this collection against another collection using its keys for
     * comparison and returns a new Collection with the values in this collection
     * that are not present in the other collection.
     *
     * Note that keys are preserved
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * @param Collection $other
     * @param \Closure $function optional function to compare values
     *
     * @return Collection
     */
    public function kDiff(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            return new static(\array_diff_key($this->getArrayCopy(), $other->getArrayCopy()), $this->type);
        }

        return new static(\array_diff_ukey($this->getArrayCopy(), $other->getArrayCopy(), $function), $this->type);
    }


    /**
     * @deprecated - use vIntersect
     *
     * @param Collection $other
     * @param \Closure $function
     * @return Collection
     */
    public function intersect(Collection $other, \Closure $function = null)
    {
        return $this->vIntersect($other, $function);
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
     * @param callable|\Closure $function Optional function to compare values
     *
     * @return Collection
     */
    public function vIntersect(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            return new static(\array_intersect($this->getArrayCopy(), $other->getArrayCopy()), $this->type);
        }

        return new static(\array_uintersect($this->getArrayCopy(), $other->getArrayCopy(), $function), $this->type);
    }

    /**
     * Returns a Collection containing all the values of this Collection that are present
     * in the other Collection. Keys are used for comparison
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * @param Collection $other
     * @param \Closure $function Optional function to compare values
     *
     * @return Collection
     */
    public function kIntersect(Collection $other, \Closure $function = null)
    {
        return new static(
            FMatch::on(Option::create($function))
                ->Monad_Option_Some(function () use ($other, $function) {
                    return \array_intersect_ukey($this->getArrayCopy(), $other->getArrayCopy(), $function);
                })
                ->Monad_Option_None(function () use ($other) {
                    return \array_intersect_key($this->getArrayCopy(), $other->getArrayCopy());
                })
                ->value(),
            $this->type
        );
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
                \array_merge($this->getArrayCopy(), $other->getArrayCopy()),
                $sortOrder
            )
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
    public function head(): Collection
    {
        return new static(array_slice($this->getArrayCopy(), 0, 1));
    }

    /**
     * Return a Collection with all but the first member of this Collection
     *
     * @return Collection
     */
    public function tail(): Collection
    {
        return new static(array_slice($this->getArrayCopy(), 1));
    }

    /**
     * Append value and return a new collection
     * NB this uses vUnion
     *
     * Value will be forced into an array if not already one
     *
     * @param mixed $value
     *
     * @return Collection
     */
    public function append($value)
    {
        $nValue = (is_array($value) ? $value : [$value]);
        return $this->vUnion(new static($nValue));
    }

    /**
     * @param string $type
     *
     * @return FTry
     */
    protected function setType($type): FTry
    {
        return FMatch::on($type)
            ->string(function ($type) {
                $this->type = $type;
                return FTry::with($type);
            })
            ->any(function () {
                return FTry::with(function () {
                    return new \RuntimeException('Type must be specified by string');
                });
            })
            ->value();
    }

    /**
     * @param array $value
     *
     * @return FTry
     */
    protected function setTypeFromValue(array $value): FTry
    {
        //required to be defined as a var so it can be called in next statement
        $basicTest = function () use ($value) {
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
        $type = FMatch::on($firstValue)
            ->Monad_Option_Some(
                function ($option) {
                    return Option::create(gettype($option->value()));
                }
            )
            ->Monad_Option_None(function () {
                return new None();
            })
            ->value();

        //@var Option
        //MatchLegalType requires to be defined separately as it is used twice
        //in the next statement
        $matchLegalType = FTry::with(
            FMatch::on($type)
                ->Monad_Option_None()
                ->Monad_Option_Some(
                    function ($v) use ($firstValue) {
                        FMatch::on($v->value())
                            ->test('object', function ($v) use ($firstValue) {
                                $this->setType(get_class($firstValue->value()));
                                return new Some($v);
                            })
                            ->test('string', function ($v) {
                                $this->setType($v);
                                return new Some($v);
                            })
                            ->test('integer', function ($v) {
                                $this->setType($v);
                                return new Some($v);
                            })
                            ->test('double', function ($v) {
                                $this->setType($v);
                                return new Some($v);
                            })
                            ->test('boolean', function ($v) {
                                $this->setType($v);
                                return new Some($v);
                            })
                            ->test('resource', function ($v) {
                                $this->setType($v);
                                return new Some($v);
                            })
                            ->any(function () {
                                return new None();

                            });
                    }
                )
                ->any(function () {
                    return new None();
                })
        );

        return FTry::with(function () use ($matchLegalType) {
            return $matchLegalType->value();
        });
    }

    /**
     * @param array $values
     *
     * @return FTry
     */
    protected function setValue(array $values): FTry
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
     * @return mixed
     */
    protected function callFunction(\Closure $function, $value, array $args = [])
    {
        if ($value instanceof Monadic && !$value instanceof Collection) {
            return $value->bind($function, $args);
        }

        $val = ($value instanceof \Closure ? $value() : $value);
        array_unshift($args, $val);

        return call_user_func_array($function, $args);
    }
}
