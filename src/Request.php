<?php 

namespace RequestService;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

class Request extends BaseRequest
{
	private $config;

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function sendRequest(
		string $service,
		string $method,
		string $uri,
		array $header = [],
		array $body = []
	): array {
		try {
			if (!isset($this->config[$service])) {
				throw new \Exception('Service config not found', 422);
			}

			$this->jsonRequest = $this->config[$service]['json'] ?? false;

			$headers = $this->prepareHeader($header);
			$body = $this->prepareBody($body);
			$url  = $this->prepareUrl($this->config[$service]['url'], $uri);

			$response = $this->newGuzzle()->$method($url, array_merge($headers, $body));

			if (strtolower($method) == 'delete') {
				return [];
			}

			return json_decode($response->getBody(), true);
		} catch (ClientException $e) {
			return [
				'message' => json_decode($e->getResponse()->getBody(), true),
				'error_code' => $e->getResponse()->getStatusCode(),
			];
		} catch (\Exception $e) {
			return [
				'message' => $e->getMessage() ?? 'Request error',
				'error_code' => $e->getCode() ?? 500,
			];
		}
	}

    /**
     * @codeCoverageIgnore
     */
	public function newGuzzle()
	{
		return new Guzzle();
	}
}
