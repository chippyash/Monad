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
 * Can return a value
 * Implements part of Monadic Interface
 */
trait ReturnValueAble
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Return value of Monad
     * Does not manipulate the value in any way
     *
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}