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
     *
     * @return mixed
     */
    public function get();

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
     * Static factory creator for the Monad
     *
     * @param mixed $value
     *
     * @return Monadic
     */
    public static function create($value);
}