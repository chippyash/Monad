<?php
/**
 * FMatch
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
declare(strict_types=1);
namespace Monad;

class FMatch implements Monadic
{
    use CallFunctionAble;
    use FlattenAble;
    use ReturnValueAble;

    /**
     * @var bool
     */
    protected $isMatched = false;

    /**
     * @param mixed $value
     * @param bool $isMatched
     */
    public function __construct($value, $isMatched = false)
    {
        $this->value = $value;
        $this->isMatched = $isMatched;
    }

    /**
     * Syntactic proxy for create()
     * @param mixed $value
     *
     * @return FMatch
     *@see create()
     *
     */
    public static function on($value): FMatch
    {
        return static::create($value);
    }

    /**
     * Magic unknown method that proxies native type and class type matching
     *
     * @param string $method
     * @param array $args If args[0] set, then use as concrete value or function to
     * bind onto current value
     *
     * @return FMatch
     */
    public function __call($method, $args): FMatch
    {
        if ($this->isMatched) {
            return new static($this->value, $this->isMatched);
        }

        if ($this->matchOnNative($method) || $this->matchOnClassName($method)) {
            if (isset($args[0])) {
                if (is_callable($args[0]) && !$args[0] instanceof Monadic) {
                    return new static($args[0]($this->value), true);
                }

                return new static($args[0], true);
            }

            return new static($this->value, true);
        }

        return new static($this->value);
    }

    /**
     * Match anything. Usually called last in Match chain
     *
     * @param callable|\Closure $function
     * @param array $args Optional additional arguments to function
     * @return FMatch
     */
    public function any(\Closure $function = null, array $args = []): FMatch
    {
        if ($this->isMatched) {
            return new static($this->value, $this->isMatched);
        }

        if (is_null($function)) {
            return new static($this->value, true);
        }

        return new static($this->callFunction($function, $this->value, $args), true);
    }

    /**
     * Test current value for exact equality to the test value
     *
     * @param mixed $test Value to test against
     *
     * @param \Closure $function Function that is used if test is true
     * @param array $args Optional additional arguments to function
     *
     * @return FMatch
     */
    public function test($test, \Closure $function = null, array $args = []): FMatch
    {
        if ($this->isMatched) {
            return new static($this->value, $this->isMatched);
        }
        if ($this->value === $test) {
            if (is_null($function)) {
                return new static($this->value, true);
            }

            return new static($this->callFunction($function, $this->value, $args), true);
        }

        return new static($this->value());
    }


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
     * Bind match value with function.
     *
     * Function is in form f($value) {}
     *
     * You can pass additional parameters in the $args array in which case your
     * function should be in the form f($value, $arg1, ..., $argN) {}
     *
     * @param \Closure $function
     * @param array $args additional arguments to pass to function
     *
     * @return FMatch
     */
    public function bind(\Closure $function, array $args = []): FMatch
    {
        return new static($this->callFunction($function, $this->value, $args), $this->isMatched);
    }

    /**
     * Static factory creator for the Monad
     *
     * @param mixed $value
     *
     * @return FMatch
     */
    public static function create($value): FMatch
    {
        return new static($value);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function matchOnNative($name): bool
    {
        switch(strtolower($name)) {
            case 'string':
                return is_string($this->value);
            case 'integer':
            case 'int':
            case 'long':
                return is_int($this->value);
            case 'float':
            case 'double':
            case 'real':
                return is_double($this->value);
            case 'null':
                return is_null($this->value);
            case 'array':
                return is_array($this->value);
            case 'boolean':
            case 'bool':
                return is_bool($this->value);
            case 'callable':
            case 'function':
            case 'closure':
                return is_callable($this->value);
            case 'file':
                return is_file($this->value);
            case 'dir':
            case 'directory':
                return is_dir($this->value);
            case 'object':
                return is_object($this->value);
            case 'scalar':
                return is_scalar($this->value);
            case 'numeric':
                return is_numeric($this->value);
            case 'resource':
                return is_resource($this->value);
            default:
                return false;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function matchOnClassName($name): bool
    {
        $className = str_replace('_', '\\', $name);

        if (!class_exists($className)) {
            return false;
        }

        if ($this->value instanceof $className) {
            return true;
        }

        return false;
    }
}
