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
 * A Some Option has a value
 */
class Some extends Option
{
    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Return Some or None as a result of bind
     *
     * @param \Closure $function Ignored
     * @param array $args Ignored
     *
     * @return Some|None
     */
    public function bind(\Closure $function, array $args = [])
    {

    }
}