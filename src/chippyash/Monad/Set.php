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
 */
class Set extends Collection
{
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
        if (!empty($value))
        {
            $this->exchangeArray($this->checkUniqueness($value));
        }
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
        try
        {
            $toTest = end($values);
            reset($values);
            (string) $toTest;

            //do the simple
            return array_values(array_unique($values));
        }
        catch (\Exception $e)
        {
            //slower but effective
            return array_values(array_map(
                    function ($key) use ($values)
                    {
                        return $values[$key];
                    },
                    array_keys(array_unique(array_map(
                        function ($item)
                        {
                            return serialize($item);
                        },
                        $values
                    ))))
            );
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
     * @param Set $other
     * @param callable|\Closure $function Optional function to compare values
     *
     * @return Set
     */
    public function vIntersect(Collection $other, \Closure $function = null)
    {
        $function = (is_null($function) ? $this->equalityFunction(): $function);

        return parent::vIntersect($other, $function);
    }

    /**
     * Compares this Set against another Set and returns a new Set
     * with the values in this Set that are not present in the other collection.
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
    public function diff(Collection $other, \Closure $function = null)
    {
        $function = (is_null($function) ? $this->equalityFunction(): $function);

        return parent::diff($other, $function);
    }

    /**
     * Provide equality check function
     *
     * @return \Closure
     */
    private function equalityFunction()
    {
        return function ($a, $b)
        {
            if (is_object($a))
            {
                $a = \serialize($a);
                $b = \serialize($b);
            }

            return ($a === $b ? 0 : ($a < $b ? -1 : 1));
        };
    }
}