<?php

namespace Sarus\Request;

/**
 * @see \Serializable
 */
trait SerializableTrait
{
    public function serialize()
    {
        $data = get_object_vars($this);
        $keysToUnserialize = [];

        foreach ($data as $key => &$value) {
            if (is_object($value)) {
                $value = serialize($value);
                $keysToUnserialize[$key] = true;
            }
        }

        return \json_encode([
            'data' => $data,
            'meta' => $keysToUnserialize
        ]);
    }

    public function unserialize($serialized)
    {
        $data = \json_decode($serialized, true);

        foreach ($data['data'] as $property => $value) {
            if (isset($data['meta'][$property])) {
                $value = unserialize($value);
            }

            $this->{$property} = $value;
        }
    }
}
