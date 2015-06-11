<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Monad;

use Monad\FTry\Success;
use Monad\FTry\Failure;

/**
 * Functional Try
 */
abstract class FTry extends Monad
{
    /**
     * Create a concrete FTry. If value results in an exception being thrown then Failure else Success
     *
     * @param mixed $value Value
     *
     * @return Failure|Success
     */
    public static function create($value)
    {
        if ($value instanceof \Exception) {
            return new Failure($value);
        }
        try {
            if ($value instanceof \Closure) {
                //test to ensure function doesn't throw exception
                $value();
            } elseif ($value instanceof Monadic) {
                //test to ensure enclosed Monad value isn't an exception
                self::create($value->flatten());
            }

            return new Success($value);

        } catch (\Exception $e) {
            return new Failure($e);
        }
    }

    /**
     * Proxy to create()
     *
     * @param mixed $value
     * @return Failure|Success
     */
    public static function with($value)
    {
        return self::create($value);
    }

    /**
     * Return FTry value if Success else the elseValue
     *
     * @param mixed $elseValue
     *
     * @return mixed
     */
    public function getOrElse($elseValue)
    {
        if ($this instanceof Success) {
            return $this->value();
        }

        return $elseValue;
    }

    /**
     * Is this option a Success?
     *
     * @return bool
     */
    abstract public function isSuccess();
}