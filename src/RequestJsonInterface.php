<?php 

namespace RequestService;

interface RequestJsonInterface
{
	public function sendRequest(
		string $service,
		string $method,
		string $uri,
		array $header,
		array $body = []
	): array;

	public function getConfigValue(): array;
}