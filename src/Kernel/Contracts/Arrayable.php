<?php


namespace Lmh\DouyinOpenApi\Kernel\Contracts;

use ArrayAccess;

interface Arrayable extends ArrayAccess
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array;
}
