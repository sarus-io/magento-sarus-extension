<?php

namespace Sarus;

use Sarus\Client\Exception\HttpException;
use Sarus\Client\Exception\RuntimeException;
use Sarus\Request\Enrollment\Deactivate;
use Sarus\Request\Enrollment\GetList;
use Sarus\Request\Ping;
use Sarus\Request\Product\Purchase;
use Sarus\Request\Product\Unlink;
use Sarus\Request\User;

class Sdk
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $uuids
     * @param $user
     * @param array $extraData
     *
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function purchaseProduct(array $uuids, User $user, array $extraData = [])
    {
        return $this->handleRequest(new Purchase($uuids, $user, $extraData));
    }

    /**
     * @param string $uuid
     *
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function unlinkProduct($uuid)
    {
        return $this->handleRequest(new Unlink($uuid));
    }

    /**
     * @param string $email
     *
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function listEnrollments($email)
    {
        return $this->handleRequest(new GetList($email));
    }

    /**
     * @param string $email
     * @param array|string[] $productUuids
     *
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function deactivateEnrollments($email, array $productUuids)
    {
        return $this->handleRequest(new Deactivate($email, $productUuids));
    }

    /**
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function ping()
    {
        return $this->handleRequest(new Ping());
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function handleRequest(Request $request)
    {
        return $this->client->request($request);
    }
}
