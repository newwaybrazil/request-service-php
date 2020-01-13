<?php

namespace RequestService;

use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

class Request extends BaseRequest
{
    private $config;

    /**
     * Constructor
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * method sendRequest
     * send request to a specific service with params
     * @param string $service
     * @param string $method
     * @param string $uri
     * @param array $header
     * @param array $body
     * @return mixed
     */
    public function sendRequest(
        string $service,
        string $method,
        string $uri,
        array $header = [],
        array $body = []
    ) {
        try {
            if (!isset($this->config[$service])) {
                throw new Exception('Service config not found', 422);
            }

            $this->jsonRequest = $this->config[$service]['json'] ?? true;

            $headers = $this->prepareHeader($header);
            $body = $this->prepareBody($body);
            $url  = $this->prepareUrl($this->config[$service]['url'], $uri);

            $response = $this->newGuzzle()->$method($url, array_merge($headers, $body));

            if (isset($headers['headers']['stream']) && $headers['headers']['stream']) {
                return base64_encode($response->getBody()->getContents());
            }

            if (strtolower($method) == 'delete') {
                return [];
            }

            if ($this->jsonRequest) {
                return json_decode($response->getBody(), true);
            }

            return $response->getBody();
        } catch (ClientException $e) {
            $exception = json_decode($e->getResponse()->getBody(), true);
            $exception['error_code'] = $e->getResponse()->getStatusCode();

            return $exception;
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage() ?? 'Request error',
                'error_code' => $e->getCode() ?? 500,
            ];
        }
    }

    /**
     * @codeCoverageIgnore
     * method newGuzzle
     * create and return new GuzzleHttp\Client object
     * (should not contain any logic, just instantiate the object and return it)
     * @return GuzzleHttp\Client
     */
    public function newGuzzle()
    {
        return new Guzzle();
    }
}
