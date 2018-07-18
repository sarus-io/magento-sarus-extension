<?php

namespace Sarus\Request;

/**
 * @see Request
 */
trait ArraySerializableTrait
{
    public function toArray()
    {
        return [
            'path'   => $this->getPath(),
            'body'   => $this->getBody(),
            'method' => $this->getMethod(),
        ];
    }
}
