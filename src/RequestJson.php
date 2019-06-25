<?php 

namespace RequestService;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

abstract class RequestJson
{
	private $guzzle;

	public function __construct(Guzzle $guzzle)
	{
		$this->guzzle = $guzzle;
	}

	public function sendRequest(
		string $service,
		string $method,
		string $uri,
		array $header,
		array $body = []
	): array {
		try {
			$headers = $this->prepareHeader($header, $service);
			$body = $this->prepareBody($body);
			$url  = $this->getBaseUrl($service).$uri;

			$response = $this->guzzle->$method($url, array_merge($headers, $body));

			if (strtolower($method) == 'delete') {
				return [];
			}

			return json_decode($response->getBody(), true);
		} catch (ClientException $e) {
			if ($e->getResponse()->getStatusCode() == 401) {
				$this->unauthorized();
			}

			$response = json_decode($e->getResponse()->getBody(), true);
			$response['error_code'] = $e->getResponse()->getStatusCode();

			return $response;
		} catch (\Exception $e) {
			return [
				'error_code' => 500,
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

	public function prepareHeader(array $header, string $service): array
	{
		return [
			'headers' => array_merge(
				[
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Context' => $this->getConfigValue($service)['context'],
				],
				$header
			)
		];
	}

	public function getBaseUrl(string $service): string
	{
		$url = $this->getConfigValue($service)['url'];
		if (strpos($url, '/') === false) {
			return "$url/";
		}

		return $url;
	}

	public function getConfigValue(): array
	{
		throw new \Exception('No found config', 422);
	}
}
