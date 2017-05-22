<?php
/**
 * Monad
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Monad;

/**
 * A Collection that enforces a string (hash) key for each item in the collection
 */
class Map extends Collection
{
    /**
     * A Collection enforcing string keys
     *
     * If you do not specify $type, it will be inferred from the first item in the
     * value array.  If value is empty and type is not specified, will throw an exception
     *
     * @param array $value String keyed associative array of data to set
     * @param string $type Specific type of data contained in the array
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $value = [], $type = null)
    {
        if (!empty($value)) {
            if (!$this->checkHash($value)) {
                throw new \InvalidArgumentException('value is not a hashed array');
            }
        }
        parent::__construct($value, $type);
    }

    /**
     * @param array $value
     *
     * @return bool
     */
    protected function checkHash(array $value)
    {
        return array_reduce(
            array_keys($value),
            function ($carry, $val) {
                if (!is_string($val)) {
                    return false;
                }
                return $carry;
            },
            true
        );
    }
}