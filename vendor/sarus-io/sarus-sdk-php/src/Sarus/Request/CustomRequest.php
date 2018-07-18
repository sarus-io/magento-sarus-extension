<?php

namespace Sarus\Request;

use Sarus\Request;

class CustomRequest implements Request
{
    use SerializableTrait;
    use ArraySerializableTrait;
    use JsonSerializableTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array|null
     */
    private $body;

    /**
     * @param string $path
     * @param string $method
     * @param array|null $body
     */
    public function __construct($path, $method, array $body = null)
    {
        $this->path   = $path;
        $this->method = $method;
        $this->body   = $body;
    }

    /**
     * @param array $data
     * @return CustomRequest
     */
    public static function fromArray(array $data)
    {
        if (empty($data['path']) || !is_string($data['path'])) {
            throw new \InvalidArgumentException('Parameter `path` is required and should be a non empty string');
        }

        if (empty($data['method']) || !is_string($data['method'])) {
            throw new \InvalidArgumentException('Parameter `method` is required and should be a non empty string');
        }

        return new self(
            (string) $data['path'],
            (string) $data['method'],
            (isset($data['body']) ? ((array) $data['body']) : null)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }
}
