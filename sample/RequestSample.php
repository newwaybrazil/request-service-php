<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use RequestService\Request;

$config = [
	'your-service' => [
		'url' => 'https://jsonplaceholder.typicode.com',
	],
	'your-service-json' => [
		'url' => 'https://jsonplaceholder.typicode.com',
		'json' => true,
	],
];

$sample = new Request($config);
$response = $sample->sendRequest('your-service-json', 'GET', 'todos/1');

print_r($response);
