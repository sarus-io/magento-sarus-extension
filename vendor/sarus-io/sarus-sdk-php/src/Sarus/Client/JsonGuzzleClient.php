<?php

namespace Sarus\Client;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Sarus\Client;
use Sarus\Client\Exception\HttpException;
use Sarus\Client\Exception\RuntimeException;
use Sarus\Request;
use Sarus\Response;

class JsonGuzzleClient implements Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    public function __construct(\GuzzleHttp\Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * {@inheritdoc}
     */
    public function request(Request $request)
    {
        $jsonBody = null;
        if (null !== $request->getBody()) {
            $jsonBody = $this->encodeJson($request->getBody());
        }

        try {
            $httpResponse = $this->guzzle->request(
                $request->getMethod(),
                $request->getPath(),
                ['body' => $jsonBody]
            );
        } catch (RequestException $e) {
            throw new HttpException($e->getMessage(), $e->getRequest(), $e->getResponse());
        } catch (GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $body = (string) $httpResponse->getBody();
        $parsedBody = [];

        if ($body) {
            $parsedBody = $this->decodeJson($body);
        }

        return new Response($parsedBody);
    }

    /**
     * @param string $body
     * @return array
     */
    private function decodeJson($body)
    {
        try {
            return \GuzzleHttp\json_decode($body, true);
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function encodeJson(array $data)
    {
        try {
            return \GuzzleHttp\json_encode($data);
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
