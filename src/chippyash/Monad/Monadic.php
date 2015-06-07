<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad;

/**
 * Monad Interface Definition
 */
interface Monadic
{
    /**
     * Return value of Monad
     * If value === \Monadic, will return its value, not the Monad
     *
     * @return mixed
     */
    public function get();

    /**
     * Return value of Monad as a base type.
     * If value === \Closure, will evaluate the function and return it's value
     *
     * @return mixed
     */
    public function flatten();

    /**
     * Map monad with function.
     *
     * Function is in form f($value) {}
     *
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN) {}
     *
     * @param Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return Monadic
     */
    public function map(\Closure $function, array $args = []);

    /**
     * Map then flatten
     *
     * @param callable $function
     * @param array $args Optional additional arguments
     *
     * @return monadic
     */
    public function flatMap(\Closure $function, array $args = []);

    /**
     * Static factory creator for the Monad
     *
     * @param mixed $value
     *
     * @return Monadic
     */
    public static function create($value);
}