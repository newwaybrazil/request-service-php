<?php 

namespace RequestService;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

abstract class RequestJson
{
	public function sendRequest(
		string $service,
		string $method,
		string $uri,
		array $header,
		array $body = []
	): array {
		try {
			$requestConfig = $this->getConfigValue();
			if (!isset($requestConfig[$service])) {
				throw new \Exception('Service config not found', 422);
			}

			$headers = $this->prepareHeader($header);
			$body = $this->prepareBody($body);
			$url  = $this->prepareUrl($requestConfig[$service]['url'], $uri);

			$response = $this->newGuzzle()->$method($url, array_merge($headers, $body));

			if (strtolower($method) == 'delete') {
				return [];
			}

			return json_decode($response->getBody(), true);
		} catch (ClientException $e) {
			$response = json_decode($e->getResponse()->getBody(), true);
			$response['error_code'] = $e->getResponse()->getStatusCode();

			return $response;
		} catch (\Exception $e) {
			return [
				'message' => $e->getMessage() ?? 'Request error',
				'error_code' => $e->getCode() ?? 500,
			];
		}
	}

	public function prepareBody(array $body = []): array
	{
		if (count($body)) {
			return [
				'json' => $body
			];
		}

		return [];
	}

	public function prepareHeader(array $header): array
	{
		return [
			'headers' => array_merge(
				[
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
				],
				$header
			)
		];
	}

	public function prepareUrl(string $url, string $uri): string
	{
		$protocol = '';
		if (strpos($url, 'http') !== false) {
			$url = explode('//', $url);

			$protocol = $url[0].'//';
			$url = $url[1];
		}

		$url = str_replace('/', '', $url);

		if (strpos($uri, '/') === false || strpos($uri, '/') > 0) {
			$uri = "/$uri";
		}

		return "$protocol$url$uri";
	}

    /**
     * @codeCoverageIgnore
     */
	public function getConfigValue(): array
	{
		throw new \Exception('No found config', 422);
	}

    /**
     * @codeCoverageIgnore
     */
	public function newGuzzle()
	{
		return new Guzzle();
	}
}
