<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
declare(strict_types=1);
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
     * @param \Closure $function
     * @param array $args
     * @param mixed $noneValue Optional value to test for None
     *
     * @return Some|None
     */
    public function bind(\Closure $function, array $args = [], $noneValue = null): Option
    {
        return Option::create($this->callFunction($function, $this->value, $args), $noneValue);
    }
}
