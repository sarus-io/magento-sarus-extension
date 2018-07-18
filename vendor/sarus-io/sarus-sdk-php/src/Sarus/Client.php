<?php

namespace Sarus;

use Sarus\Client\Exception\HttpException;
use Sarus\Client\Exception\RuntimeException;

interface Client
{
    /**
     * @param Request $request
     * @return Response
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function request(Request $request);

}
