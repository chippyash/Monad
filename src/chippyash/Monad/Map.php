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
 * A Collection that enforces a string (hash) key for each item in the collection.
 *
 * A Map has operations that depend on the hash keys
 */
class Map extends Collection
{
    /**
     * Bad Method call error template
     */
    const ERR_TPL_BADM = '%s is not a supported method for Maps';

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
     * Append value and return a new collection
     *
     * The appending is based on the hashed keys
     *
     * Overrides ancestor
     *
     * @param array $value
     *
     * @return Collection
     * @throws \InvalidArgumentException
     */
    public function append($value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Appended value must be array');
        }
        return $this->kUnion(new static($value));
    }

    /**
     * Value intersection is meaningless for a Map
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function vIntersect(Collection $other, \Closure $function = null)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
    }

    /**
     * Value union is meaningless for a Map
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function vUnion(Collection $other, $sortOrder = SORT_REGULAR)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
    }

    /**
     * Value difference is meaningless for a Map
     *
     * @inheritdoc
     * @throws \BadMethodCallException
     */
    final public function vDiff(Collection $other, \Closure $function = null)
    {
        throw new \BadMethodCallException(sprintf(self::ERR_TPL_BADM, __METHOD__));
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