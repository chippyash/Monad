<?php
/**
 * Monad
 *
 * @author    Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license   GPL V3+ See LICENSE.md
 */

namespace Monad;

/**
 * A Monadic Set
 *
 * A Set can only have unique values
 * The keys for sets are ignored.  Only the values are important
 */
class Set extends Collection
{
    /**
     * Bad Method call error template
     */
    const ERR_TPL_BADM = '%s is not a supported method for Sets';

    /**
     * Set constructor.
     *
     * @param array  $value Values will be forced to be unique
     * @param string $type  optional class name to enforce type
     */
    public function __construct(array $value = [], $type = null)
    {
        //do parameters pass normal construction rule?
        parent::__construct($value, $type);

        //If we passed in values then check them
        if (!empty($value)) {
            $this->exchangeArray($this->checkUniqueness($value));
        }
    }

    /**
     * Returns a Set containing all the values of this Set that are present
     * in the other Set.
     *
     * If the optional comparison function is supplied it must have signature
     * function(mixed $a, mixed $b){}. The comparison function must return an integer
     * less than, equal to, or greater than zero if the first argument is considered
     * to be respectively less than, equal to, or greater than the second.
     *
     * If the comparison function is not supplied, a built in one will be used
     *
     * @param Set               $other
     * @param callable|\Closure $function Optional function to compare values
     *
     * @return Set
     */
    public function vIntersect(Collection $other, \Closure $function = null)
    {
        $function = (is_null($function) ? $this->equalityFunction() : $function);

        return parent::vIntersect($other, $function);
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
     * If the comparison function is not supplied, a built in one will be used
     *
     * @param Set $other
     * @param \Closure $function optional function to compare values
     *
     * @return Set
     */
    public function vDiff(Collection $other, \Closure $function = null)
    {
        $function = (is_null($function) ? $this->equalityFunction() : $function);

        return parent::vDiff(
            $other, $function
        );
    }

    /**
     * Bind monad with function.  Function is in form f($value){}
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN)
     *
     * @param \Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return Set
     */
    public function bind(\Closure $function, array $args = [])
    {
        $res = $this->callFunction($function, $this, $args);

        return ($res instanceof Set ? $res : new static(is_array($res)? $res :[$res]));
    }

    /**
     * Key intersection is meaningless for a set
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function kIntersect(Collection $other, \Closure $function = null)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
    }

    /**
     * Key union is meaningless for a set
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function kUnion(Collection $other)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
    }

    /**
     * Key difference is meaningless for a set
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function kDiff(Collection $other, \Closure $function = null)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
    }


    /**
     * Make sure that values are unique
     *
     * @param array $values Values to check
     *
     * @return array
     */
    protected function checkUniqueness(array $values)
    {
        \set_error_handler(
            function ($errno, $errstr, $errfile, $errline) {
                if (E_RECOVERABLE_ERROR===$errno) {
                    throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
                }
                return false;
            },
            E_RECOVERABLE_ERROR
        );

        try {
            //see if we can turn a value into a string
            $toTest = end($values);
            reset($values);
            (string) $toTest; //this will throw an exception if it fails
            \restore_error_handler();

            //do the simple
            return array_values(array_unique($values));
        } catch (\ErrorException $e) {
            \restore_error_handler();

            //slower but effective
            return array_values(
                array_map(
                    function ($key) use ($values) {
                        return $values[$key];
                    },
                    array_keys(
                        array_unique(
                            array_map(
                                function ($item) {
                                    return serialize($item);
                                },
                                $values
                            )
                        )
                    )
                )
            );
        }
    }

    /**
     * Provide equality check function
     *
     * @return \Closure
     */
    private function equalityFunction()
    {
        return function ($a, $b) {
            if (is_object($a)) {
                $a = \serialize($a);
                $b = \serialize($b);
            }

            return ($a === $b ? 0 : ($a < $b ? -1 : 1));
        };
    }
}