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
 * Key value pair collection object that is mutable
 */
class MutableCollection extends Collection
{
    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        \ArrayObject::offsetSet($offset, $value);
    }

    /**
     * ArrayAccess Interface
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        \ArrayObject::offsetUnset($offset);
    }
}
