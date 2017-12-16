<?php

namespace Customerio\Endpoint;

use Customerio\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Base
{
    /** @var Client */
    protected $client;

    /**
     * Base constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $customerId
     * @return string
     */
    protected function customerPath($customerId)
    {
        return "customers/".$customerId;
    }

    /**
     * @param $message
     * @param $method
     */
    protected function mockException($message, $method)
    {
        throw new RequestException($message, (new Request($method, '/')));
    }
}