<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sarus\Request\Enrollment\Deactivate;
use Sarus\Request\Enrollment\GetList;
use Sarus\Request\Ping;
use Sarus\Request\Product\Purchase;
use Sarus\Request\Product\Unlink;
use Sarus\Request\User;
use Sarus\Sdk;

class SdkTest extends TestCase
{
    const USER_EMAIL = 'test@test.com';

    /**
     * @var Sdk
     */
    private $sdk;

    /**
     * @var \Sarus\Client|ObjectProphecy
     */
    private $clientMock;

    protected function setUp()
    {
        $this->clientMock = $this->prophesize(\Sarus\Client::class)->reveal();
        $this->sdk = new Sdk($this->clientMock);
    }

    public function test_it_purchases_product()
    {
        $user = (new User(
            self::USER_EMAIL,
            'test_name',
            'test-last_name',
            96
        ));
        $products = ['PRODUCT_UUID'];

        $this->sdk->purchaseProduct($products, $user);

        $this->clientMock->request(new Purchase($products, $user))->shouldHaveBeenCalled();
    }

    public function test_it_successfully_returns_enrollment_data()
    {
        $email = self::USER_EMAIL;

        $this->sdk->listEnrollments($email);

        $this->clientMock->request(new GetList($email))->shouldHaveBeenCalled();
    }

    public function test_it_deactivates_enrollment()
    {
        $email = self::USER_EMAIL;
        $products = ['PRODUCT_UUID'];

        $this->sdk->deactivateEnrollments($email, $products);

        $this->clientMock->request(new Deactivate($email, $products))->shouldHaveBeenCalled();
    }

    public function test_it_unlinks_product()
    {
        $product = 'PRODUCT_UUID';
        $this->sdk->unlinkProduct($product);
        $this->clientMock->request(new Unlink($product))->shouldHaveBeenCalled();
    }

    public function test_it_pings()
    {
        $this->sdk->ping();
        $this->clientMock->request(new Ping())->shouldHaveBeenCalled();
    }
}
