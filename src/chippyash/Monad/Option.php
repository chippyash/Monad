<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

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
     * Proxy to option()
     *
     * @param mixed $value Value
     * @param mixed $noneValue Optional value to test for None
     *
     * @return Some|None
     */
    public static function create($value = null, $noneValue = null)
    {
        if ($value === $noneValue) {
            return new None();
        }

        return new Some($value);
    }
}
