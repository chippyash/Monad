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

/**
 * Trait providing callFunction
 */
trait CallFunctionAble
{
    /**
     * Call function on value
     *
     * @param \Closure $function
     * @param mixed $value
     * @param array $args additional arguments to pass to function
     *
     * @return mixed
     */
    protected function callFunction(\Closure $function, $value, array $args = [])
    {
        if ($value instanceof Monadic) {
            return $value->bind($function, $args);
        }

        $val = ($value instanceof \Closure ? $value() : $value);
        array_unshift($args, $val);

        return call_user_func_array($function, $args);
    }
}
