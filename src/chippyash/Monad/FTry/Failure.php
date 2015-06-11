<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad\FTry;


use Monad\FTry;

class Failure extends FTry
{
    /**
     * Constructor
     * @param \Exception $value
     */
    public function __construct(\Exception $value)
    {
        $this->value = $value;
    }

    /**
     * Always return another instance of Failure
     *
     * @param Exception $value Ignored
     *
     * @return Failure
     */
    public static function create($value = null)
    {
        if ($value instanceof \Exception) {
            return new self($value);
        }

        return new self(new \RuntimeException('Creating Failure with no exception'));
    }

    /**
     * Always return another instance of failure
     *
     * @param \Closure $function Ignored
     * @param array $args Ignored
     *
     * @return Failure
     */
    public function bind(\Closure $function, array $args = [])
    {
        return new self($this->value);
    }

    /**
     * Throw this failure as a php exception
     * We use 'pass' as `throw` is a PHP reserved word and I'm a Rugby player
     *
     * @throw \Exception
     */
    public function pass()
    {
        throw $this->value;
    }

    /**
     * Is this option a Success?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return false;
    }
}