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

/**
 * A simple identity monad
 */
class Identity extends Monad
{
    /**
     * Constructor
     *
     * @param mixed $value value for the Identity
     *
     * @return Identity
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
