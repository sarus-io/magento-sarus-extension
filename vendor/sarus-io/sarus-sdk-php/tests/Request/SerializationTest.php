<?php

namespace Test\Request;

use PHPUnit\Framework\TestCase;
use Sarus\Request;
use Sarus\Request\Enrollment\Deactivate;
use Sarus\Request\Enrollment\GetList;
use Sarus\Request\Ping;
use Sarus\Request\Product\Purchase;
use Sarus\Request\Product\Unlink;
use Sarus\Request\User;

class SerializationTest extends TestCase
{
    /**
     * @dataProvider requestsDataProvider
     */
    public function test_serialization(Request $request)
    {
        $serialized = serialize($request);
        $unserializedRequest = unserialize($serialized);

        $this->assertRequestsParametersEquals($request, $unserializedRequest);
    }

    /**
     * @dataProvider requestsDataProvider
     */
    public function test_json_serialization(Request $request)
    {
        $serialized = \json_encode($request);

        $data = \json_decode($serialized, true);
        $unserializedRequest = Request\CustomRequest::fromArray($data);

        $this->assertRequestsParametersEquals($request, $unserializedRequest);
    }

    /**
     * @dataProvider requestsDataProvider
     */
    public function test_array_serialization(Request $request)
    {
        $serialized = $request->toArray();
        $unserializedRequest = Request\CustomRequest::fromArray($serialized);

        $this->assertRequestsParametersEquals($request, $unserializedRequest);
    }

    public function requestsDataProvider()
    {
        return [
            0 => [
                new Unlink('product_uuid')
            ],

            1 => [
                call_user_func(function() {
                    $user = (new User(
                        'test@test.com',
                        'test_name',
                        'test-last_name',
                        96
                    ))
                        ->setCity('Milwaukee')
                        ->setCountry('USA')
                        ->setRegion('Wisconsin')
                        ->setAddress1('53 Creek Lane')
                        ->setPostalCode('53204')
                    ;

                    $products = ['product_uuid1', 'product_uuid2'];
                    $extraData = ['company_products' => ['product_uuid2']];
                    return new Purchase($products, $user, $extraData);
                })
            ],
            2 => [
                new Deactivate('test@test.com', $products = ['product_uuid1', 'product_uuid2']),
            ],
            3 => [
                new GetList('test@test.com'),
            ],
            4 => [
                new Ping(),
            ],
        ];
    }

    private function assertRequestsParametersEquals(Request $expected, Request $actual)
    {
        static::assertEquals($expected->getBody(),   $actual->getBody(), 'Parameters `body` are not equal');
        static::assertEquals($expected->getPath(),   $actual->getPath(), 'Parameters `path` are not equal');
        static::assertEquals($expected->getMethod(), $actual->getMethod(), 'Parameters `method` are not equal');
    }
}
