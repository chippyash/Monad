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
    public function __construct($value = null){}

    /**
     * Always return another instance of None
     *
     * @param mixed $value Ignored
     *
     * @return AbstractMonad
     */
    public static function create($value = null)
    {
       return new self();
    }

    /**
     * Always return another instance of None
     *
     * @param \Closure $function Ignored
     * @param array $args Ignored
     *
     * @return AbstractMonad
     */
    public function map(\Closure $function, array $args = [])
    {
        return new self();
    }

    /**
     * You cannot get the value of a None
     *
     * @throw \RuntimeException
     */
    public function get()
    {
        throw new \RuntimeException('None has no value');
    }
}