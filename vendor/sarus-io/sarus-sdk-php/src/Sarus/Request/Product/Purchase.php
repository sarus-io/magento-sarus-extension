<?php

namespace Sarus\Request\Product;

use Sarus\Request;
use Sarus\Request\User;

class Purchase implements Request
{
    use Request\SerializableTrait;
    use Request\ArraySerializableTrait;
    use Request\JsonSerializableTrait;

    /**
     * @var array
     */
    private $uuids;

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $extraData;

    public function __construct(array $uuids, User $user, array $extraData = [])
    {
        $this->uuids = $uuids;
        $this->user = $user;
        $this->extraData = $extraData;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return [
            'product_uuids' => $this->uuids,
            'user' => $this->user->toRequestArray(),
        ] + $this->extraData;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/v1/purchase';
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'POST';
    }
}
