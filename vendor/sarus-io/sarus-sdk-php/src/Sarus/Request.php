<?php

namespace Sarus;

interface Request extends \Serializable, \JsonSerializable
{
    /**
     * @return array|null
     */
    public function getBody();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return array
     */
    public function toArray();
}
