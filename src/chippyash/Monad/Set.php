<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license GPL V3+ See LICENSE.md
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
     * @param array $value Values will be forced to be unique
     * @param string $type optional class name to enforce type
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
     * Make sure that values are unique
     *
     * @param array $values Values to check
     *
     * @return array
     */
    protected function checkUniqueness(array $values)
    {
        try {
            $toTest = end($values);
            reset($values);
            (string) $toTest;
            //do the simple
            return array_values(array_unique($values));
        } catch (\Exception $e) {
            //slower but effective
            return array_values(array_map(
                function ($key) use ($values) {
                    return $values[$key];
                },
                array_keys(array_unique(array_map(
                    function ($item) {
                        return serialize($item);
                    },
                    $values
                ))))
            );
        }
    }

    public function vIntersect(Collection $other, \Closure $function = null)
    {
        if (is_null($function)) {
            $function = function ($a, $b) {
                if (is_object($a)) {
                    $a = \serialize($a);
                    $b = \serialize($b);
                }

                return ($a === $b ? 0 : ($a < $b ? -1 : 1));
            };
        }

        return parent::vIntersect($other, $function);
    }
}