<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use RequestService\RequestJson;

$config = [
	'your-service' => [
		'url' => 'https://jsonplaceholder.typicode.com',
	],
];

$sample = new RequestJson($config);
$response = $sample->sendRequest('your-service', 'GET', 'todos/1');

print_r($response);
