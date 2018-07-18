<?php

namespace Test\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sarus\Client\JsonGuzzleClient;

class JsonGuzzleClientTest extends TestCase
{
    /**
     * @var MockHandler
     */
    private $guzzleMockHandler;

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var JsonGuzzleClient
     */
    private $jsonGuzzleClient;

    protected function setUp()
    {
        $this->guzzleMockHandler = new MockHandler();
        $this->guzzleClient = new Client(['handler' => $this->guzzleMockHandler]);
        $this->jsonGuzzleClient = new JsonGuzzleClient($this->guzzleClient);
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function test_it_encodes_request_body_to_json_if_not_empty($body, $jsonBody)
    {
        $method = 'GET';
        $path = '/path';

        $this->guzzleMockHandler->append(new Response(200));

        $request = $this->mockRequest($method, $path, $body);
        $response = $this->jsonGuzzleClient->request($request);

        static::assertInstanceOf(\Sarus\Response::class, $response);

        $httpRequest = $this->guzzleMockHandler->getLastRequest();
        static::assertEquals($method, $httpRequest->getMethod());
        static::assertEquals($path, $httpRequest->getUri()->getPath());
        static::assertEquals($jsonBody, $httpRequest->getBody()->getContents());
    }

    public function requestDataProvider()
    {
        return [
            [['a' => 'test'], \GuzzleHttp\json_encode(['a' => 'test'])],
            [null, null]
        ];
    }

    /**
     * @dataProvider responseDataProvider
     */
    public function test_it_decodes_response_body_from_json_if_it_not_empty($responseBody, $responseData)
    {
        $this->guzzleMockHandler->append(new Response(200, [], $responseBody));

        $request = $this->mockRequest();
        $response = $this->jsonGuzzleClient->request($request);

        static::assertInstanceOf(\Sarus\Response::class, $response);
        static::assertEquals($responseData, $response->getData());
    }

    public function responseDataProvider()
    {
        return [
            ['{"test": ["test1", "test2"]}', \GuzzleHttp\json_decode('{"test": ["test1", "test2"]}', true)],
            [null, []]
        ];
    }

    /**
     * @expectedException \Sarus\Client\Exception\HttpException
     */
    public function test_it_maps_guzzle_exception_to_http_exception()
    {
        $this->guzzleMockHandler->append(new RequestException('Error', new Request('GET', '/uri')));
        $request = $this->mockRequest();

        $this->jsonGuzzleClient->request($request);
    }

    /**
     * @param $method
     * @param $path
     * @param $body
     * @return \Sarus\Request|ObjectProphecy
     */
    private function mockRequest($method = 'GET', $path = '/', $body = null)
    {
        /** @var $request \Sarus\Request | ObjectProphecy*/
        $request = $this->prophesize(\Sarus\Request::class);

        $request->getPath()->willReturn($path);
        $request->getMethod()->willReturn($method);
        $request->getBody()->willReturn($body);

        return $request->reveal();
    }
}
