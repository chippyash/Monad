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

class Success extends FTry
{

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        if ($value instanceof \Exception) {
            throw new \RuntimeException('Cannot construct Success with An Exception');
        }
        $this->value = $value;
    }

    /**
     * Return Success or Failure as a result of bind
     *
     * @param \Closure $function Ignored
     * @param array $args Ignored
     *
     * @return Success|Failure
     */
    public function bind(\Closure $function, array $args = [])
    {
        try {
            return FTry::create($this->callFunction($function, $this->value, $args));
        } catch (\Exception $e) {
            return new Failure($e);
        }
    }

    /**
     * Is this option a Success?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * Do nothing
     */
    public function pass()
    {
    }

}