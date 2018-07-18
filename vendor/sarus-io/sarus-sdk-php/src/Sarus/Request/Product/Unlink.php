<?php

namespace Sarus\Request\Product;

use Sarus\Request;

class Unlink implements Request
{
    use Request\SerializableTrait;
    use Request\ArraySerializableTrait;
    use Request\JsonSerializableTrait;

    /**
     * @var string
     */
    private $productUuid;

    public function __construct($productUuid)
    {
        $this->productUuid = $productUuid;
    }

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
        return '/v1/products/unlink/' . $this->productUuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'POST';
    }
}
