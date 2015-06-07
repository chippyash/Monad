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
     *
     * @return mixed
     */
    public function get()
    {
        if ($this->value instanceof Monadic) {
            return $this->value->get();
        }

        return $this->value;
    }

    /**
     * Map monad with function.  Function is in form f($value){}
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN)
     *
     * @param \Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return AbstractMonad
     */
    public function map(\Closure $function, array $args = [])
    {
        return $this::create($this->callFunction($function, $this->value, $args));
    }

    /**
     * Static factory creator for the Monad
     *
     * @param $value
     *
     * @return AbstractMonad
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
     * Proxy to map() e.g. $ret = $foo(function($val){return $val * 2;});
     * Proxy to get() e.g. $val = $foo();
     *
     * @see map()
     * @see get()
     *
     * @return mixed|AbstractMonad
     * @throw BadMethodCallException
     */
    public function __invoke()
    {
        if (func_num_args() == 0) {
            return $this->get();
        }
        if (func_get_arg(0) instanceof \Closure) {
            return call_user_func_array(array($this, 'map'), func_get_args());
        }

        throw new \BadMethodCallException('Invoke could not match get() or map()');
    }

    /**
     * Call function on value
     *
     * @param \Closure $function
     * @param mixed $value
     * @param array $args additional arguments to pass to function
     *
     * @return AbstractMonad
     */
    protected function callFunction(\Closure $function, $value, array $args = []) {
        if ($value instanceof Monadic) {
            return $value->map($function, $args);
        }
        array_unshift($args, $value);
        return call_user_func_array($function, $args);
    }
}