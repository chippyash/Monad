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
 * If constructed, holds an internal value of Some or None
 * If called statically via create() will return a Some or None value
 */
class Option extends Monad
{
    /**
     * Constructor
     * Creates an internal value of Some or None
     *
     * @param mixed|null $value
     */
    public function __construct($value = null)
    {
        $this->value = self::option($value);
    }

    /**
     * Create a concrete option. If value === null then return None else Some($value)
     * Proxy to option()
     *
     * @see option()
     *
     * @param mixed|null $value
     *
     * @return Some|None
     */
    public static function create($value = null)
    {
        return self::option($value);
    }

    /**
     * Create a concrete option with an optional None test
     *
     * @param mixed $value Value
     * @param null $noneValue Optional value to test for None
     *
     * @return Some|None
     */
    public static function option($value, $noneValue = null)
    {
        if ($value === $noneValue) {
            return new None();
        }

        return new Some($value);
    }
}
