<?php

namespace Sarus\Request;

use Sarus\Request;

class Ping implements Request
{
    use Request\SerializableTrait;
    use Request\ArraySerializableTrait;
    use JsonSerializableTrait;

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/v1/ping';
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'GET';
    }
}
