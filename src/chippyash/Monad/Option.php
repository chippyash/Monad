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

use Monad\Option\None;
use Monad\Option\Some;

/**
 * Option
 * Called statically via create() will return a Some or None value
 */
abstract class Option extends Monad
{
    /**
     * Create a concrete option. If value === null then return None else Some($value)
     *
     * @param mixed $value Value
     * @param mixed $noneValue Optional value to test for None
     *
     * @return Some|None
     */
    public static function create($value = null, $noneValue = null): Option
    {
        if ($value === $noneValue) {
            return new None();
        }

        return new Some($value);
    }

    /**
     * Return option value if Some else the elseValue
     *
     * @param mixed $elseValue
     *
     * @return mixed
     */
    public function getOrElse($elseValue)
    {
        if ($this instanceof Some) {
            return $this->value();
        }

        return $elseValue;
    }
}
