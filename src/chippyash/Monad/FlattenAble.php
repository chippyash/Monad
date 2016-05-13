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
 * Trait to flatten a Monad value
 * Implements part of Monadic Interface
 */
trait FlattenAble
{
    /**
     * Return value of Monad as a base type.
     * If value === \Closure, will evaluate the function and return it's value
     * If value === \Monadic, will recurse
     *
     * @return mixed
     */
    public function flatten()
    {
        $val = $this->value();
        if ($val instanceof \Closure) {
            return $val();
        }
        if ($val instanceof Monadic) {
            return $val->flatten();
        }

        return $val;
    }
}
