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

    /**
     * Return value of Monad as a base type.
     * If value === \Closure, will evaluate the function and return it's value
     * If value === \Monadic, will return its value, not the Monad
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

    /**
     * Call function on value
     *
     * @param \Closure $function
     * @param mixed $value
     * @param array $args additional arguments to pass to function
     *
     * @return Monadic
     */
    protected function callFunction(\Closure $function, $value, array $args = []) {
        if ($value instanceof Monadic) {
            return $value->bind($function, $args);
        }
        if ($value instanceof \Closure) {
            $val = $value();
        } else {
            $val = $value;
        }
        array_unshift($args, $val);
        return call_user_func_array($function, $args);
    }
}