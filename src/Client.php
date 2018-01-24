<?php

namespace Customerio;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;

class Client
{
    const API_ENDPOINT = 'https://track.customer.io/api/v1/';

    /** @var BaseClient $httpClient */
    private $httpClient;

    /** @var string API key */
    protected $apiKey;

    /** @var string site ID */
    protected $siteId;

    /** @var Endpoint\Events $events */
    public $events;

    /** @var Endpoint\Customers $customers */
    public $customers;

    /** @var Endpoint\Page $page */
    public $page;

    /** @var Endpoint\Campaigns */
    public $campaigns;

    /**
     * Client constructor.
     * @param string $apiKey Api Key
     * @param string $siteId Site ID.
     */
    public function __construct($apiKey, $siteId)
    {
        $this->setDefaultClient();
        $this->events = new Endpoint\Events($this);
        $this->customers = new Endpoint\Customers($this);
        $this->page = new Endpoint\Page($this);
        $this->campaigns = new Endpoint\Campaigns($this);

        $this->apiKey = $apiKey;
        $this->siteId = $siteId;
    }

    /**
     * Set default client
     */
    private function setDefaultClient()
    {
        $this->httpClient = new BaseClient();
    }

    /**
     * Sets GuzzleHttp client.
     * @param BaseClient $client
     */
    public function setClient($client)
    {
        $this->httpClient = $client;
    }

    /**
     * Sends POST request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($endpoint, $json)
    {
        $response = $this->request('POST', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * Sends DELETE request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($endpoint, $json)
    {
        $response = $this->request('DELETE', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * Sends PUT request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($endpoint, $json)
    {
        $response = $this->request('PUT', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $json
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function request($method, $endpoint, $json)
    {
        $response = $this->httpClient->request($method, self::API_ENDPOINT.$endpoint, [
            'json' => $json,
            'auth' => $this->getAuth(),
            'headers' => [
                'Accept' => 'application/json',
            ],
            'connect_timeout' => 2,
            'timeout' => 5,
        ]);

        return $response;
    }

    /**
     * Returns authentication parameters.
     * @return array
     */
    public function getAuth()
    {
        return [$this->siteId, $this->apiKey];
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function handleResponse(Response $response)
    {
        $stream = stream_for($response->getBody());
        $data = json_decode($stream->getContents());

        return $data;
    }
}
