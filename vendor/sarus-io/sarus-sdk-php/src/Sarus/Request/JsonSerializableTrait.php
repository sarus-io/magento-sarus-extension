<?php

namespace Sarus\Request;

/**
 * @see Request
 * @see \JsonSerializable
 */
trait JsonSerializableTrait
{
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
