<?php

namespace Sarus\Request\Enrollment;

use Sarus\Request;

class GetList implements Request
{
    use Request\SerializableTrait;
    use Request\ArraySerializableTrait;
    use Request\JsonSerializableTrait;

    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = (string) $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return ['email' => $this->email];
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/v1/participations';
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'GET';
    }
}
