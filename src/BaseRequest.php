<?php 

namespace RequestService;

class BaseRequest
{
	public $jsonRequest = false;

	public function prepareBody(array $body = []): array
	{
		if ($this->jsonRequest) {
			return [
				'json' => $body
			];
		}

		return $body;
	}

	public function prepareHeader(array $header): array
	{
		if ($this->jsonRequest) {
			$header = array_merge(
				$header,
				[
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
				]
			);
		}

		return [
			'headers' => $header,
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
}
