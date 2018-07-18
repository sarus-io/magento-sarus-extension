<?php

namespace Sarus\Request\Enrollment;

use Sarus\Request;

class Deactivate implements Request
{
    use Request\SerializableTrait;
    use Request\ArraySerializableTrait;
    use Request\JsonSerializableTrait;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $productUuids;


    public function __construct($email, array $productUuids)
    {
        $this->email = $email;
        $this->productUuids = $productUuids;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return [
            'email'       => $this->email,
            'product_ids' => $this->productUuids,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return  '/v1/participation/deactivate';
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'PUT';
    }
}
