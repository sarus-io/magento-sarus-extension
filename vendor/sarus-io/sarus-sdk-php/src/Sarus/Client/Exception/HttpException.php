<?php

namespace Sarus\Client\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpException extends \RuntimeException
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @param $message
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @return static
     */
    public function __construct($message, RequestInterface $request, ResponseInterface $response = null)
    {
        $this->request = $request;
        $this->response = $response;

        parent::__construct((string) $message);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response instanceof ResponseInterface;
    }
}
