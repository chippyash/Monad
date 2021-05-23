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
    public static function create($value): FTry
    {
        if ($value instanceof \Exception) {
            return new Failure($value);
        }
        try {
            if ($value instanceof \Closure) {
                //test to ensure function doesn't throw exception or is an Exception
                $potentialException = $value();
                if ($potentialException instanceof \Exception) {
                    return new Failure($potentialException);
                }
            } elseif ($value instanceof Monadic) {
                return static::create($value->flatten());
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
    public static function with($value): FTry
    {
        return static::create($value);
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
    abstract public function isSuccess(): bool;
}
