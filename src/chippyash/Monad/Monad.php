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
 * Abstract Monad
 * You'll need to supply a constructor to set the initial value in your
 * descendant class
 *
 */
abstract class Monad implements Monadic
{
    use CallFunctionAble;
    use FlattenAble;
    use ReturnValueAble;

    /**
     * Bind monad with function.  Function is in form f($value){}
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN)
     *
     * @param \Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return Monadic
     */
    public function bind(\Closure $function, array $args = [])
    {
        return $this::create($this->callFunction($function, $this->value, $args));
    }

    /**
     * Static factory creator for the Monad
     *
     * @param $value
     *
     * @return Monadic
     */
    public static function create($value)
    {
        if ($value instanceof Monadic) {
            return $value;
        }

        return new static($value);
    }

    /**
     * Some syntactic sugar
     *
     * Proxy to bind() e.g. $ret = $foo(function($val){return $val * 2;});
     * Proxy to value() e.g. $val = $foo();
     *
     * @see bind()
     * @see value()
     *
     * @return mixed|Monadic
     * @throw BadMethodCallException
     */
    public function __invoke()
    {
        if (func_num_args() == 0) {
            return $this->value();
        }
        if (func_get_arg(0) instanceof \Closure) {
            return call_user_func_array(array($this, 'bind'), func_get_args());
        }

        throw new \BadMethodCallException('Invoke could not match value() or bind()');
    }
}
