<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\Option;

use Monad\Option;

/**
 * A None Option has no value
 */
class None extends Option
{
    /**
     * Constructor
     *
     * @param null $value Ignored
     */
    public function __construct($value = null)
    {
    }

    /**
     * Always return another instance of None
     *
     * @param mixed $value Ignored
     *
     * @return None
     */
    public static function create($value = null)
    {
        return new static();
    }

    /**
     * Always return another instance of None
     *
     * @param \Closure $function Ignored
     * @param array $args Ignored
     *
     * @return None
     */
    public function bind(\Closure $function, array $args = [])
    {
        return new static();
    }

    /**
     * You cannot get the value of a None
     *
     * @throw \RuntimeException
     */
    public function value()
    {
        throw new \RuntimeException('None has no value');
    }
}
