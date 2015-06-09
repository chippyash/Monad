<?php
/**
 * FMatch
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Monad;

class Match implements Monadic
{
    use CallFunctionAble;
    use FlattenAble;
    use ReturnValueAble;

    protected $isMatched = false;

    public function __construct($value, $isMatched = false)
    {
        $this->value = $value;
        $this->isMatched = $isMatched;
    }

    /**
     * Syntactic proxy for create()
     * @see create()
     *
     * @param mixed $value
     *
     * @return Match
     */
    public static function on($value)
    {
        return self::create($value);
    }

    public function __call($method, $args)
    {
        if ($this->matchOnNative($method) || $this->matchOnClassName($method)) {
            if (isset($args[0])) {
                if (is_callable($args[0])) {
                    return new self($args[0]($this->value), true);
                } else {
                    return new self($args[0], true);
                }
            } else {
                return new self($this->value, true);
            }
        } else {
            return new self($this->value);
        }
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
     * @return Match
     */
    public function bind(\Closure $function, array $args = [])
    {
        return new self($this->callFunction($function, $this->value, $args), $this->isMatched);
    }

    /**
     * Static factory creator for the Monad
     *
     * @param mixed $value
     *
     * @return Match
     */
    public static function create($value)
    {
        return new self($value);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function matchOnNative($name)
    {
        switch(strtolower($name)) {
            case 'string' :
                return is_string($this->value);
            case 'integer' :
            case 'int' :
            case 'long':
                return is_int($this->value);
            case 'float':
            case 'double':
            case 'real':
                return is_double($this->value);
            case 'null':
                return is_null($this->value);
            case 'array' :
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
            case 'object' :
                return is_object($this->value);
            case 'scalar' :
                return is_scalar($this->value);
            case 'numeric' :
                return is_numeric($this->value);
            case 'resource' :
                return is_resource($this->value);
            default:
                return false;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function matchOnClassName($name)
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