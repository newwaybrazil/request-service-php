<?php

namespace RequestService;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestJsonTest extends TestCase
{
    /**
     * @covers \RequestService\RequestJson::__construct
     */
    public function testCreateSendRequest()
    {
        $config = [
            'back' => [
                'url' => 'localhost',
            ],
        ];

        $requestJson = new RequestJson($config);

        $this->assertInstanceOf(RequestJson::class, $requestJson);
    }

	/**
	 * @covers \RequestService\RequestJson::sendRequest
	 */
    public function testSendRequest()
    {
    	$config = [
    		'back' => [
	    		'url' => 'localhost',
    		],
    	];

    	$header = [
    		'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Context' => 'test',
    		],
    	];

    	$body = ['json' => []];

    	$guzzleMock = Mockery::mock(Guzzle::class)
    		->shouldReceive('get')
    		->once()
    		->with('localhost/auth', array_merge($header, $body))
    		->andReturnSelf()
    		->shouldReceive('getBody')
    		->once()
    		->withNoArgs()
    		->andReturn(json_encode(['response' => true]))
    		->getMock();

    	$requestJson = Mockery::mock(RequestJson::class, [$config])->makePartial();
    	$requestJson
    		->shouldReceive('newGuzzle')
    		->once()
    		->withNoArgs()
    		->andReturn($guzzleMock)
    		->shouldReceive('getConfigValue')
    		->once()
    		->withNoArgs()
    		->andReturn($config);

    	$sendRequest = $requestJson->sendRequest('back', 'get', '/auth', ['Context' => 'test']);

    	$this->assertEquals($sendRequest, ['response' => true]);
    }

	/**
	 * @covers \RequestService\RequestJson::sendRequest
	 */
    public function testSendDeleteRequest()
    {
    	$config = [
    		'back' => [
	    		'url' => 'localhost',
    		],
    	];

    	$header = [
    		'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Context' => 'test',
    		],
    	];

    	$body = ['json' => []];

    	$guzzleMock = Mockery::mock(Guzzle::class)
    		->shouldReceive('delete')
    		->once()
    		->with('localhost/auth', array_merge($header, $body))
    		->andReturn([])
    		->shouldReceive('getBody')
    		->never()
    		->withNoArgs()
    		->andReturnSelf()
    		->getMock();

    	$requestJson = Mockery::mock(RequestJson::class, [$config])->makePartial();
    	$requestJson
    		->shouldReceive('newGuzzle')
    		->once()
    		->withNoArgs()
    		->andReturn($guzzleMock)
    		->shouldReceive('getConfigValue')
    		->once()
    		->withNoArgs()
    		->andReturn($config);

    	$sendRequest = $requestJson->sendRequest('back', 'delete', '/auth', ['Context' => 'test']);

    	$this->assertEquals($sendRequest, []);
    }

	/**
	 * @covers \RequestService\RequestJson::sendRequest
	 */
    public function testSendRequestAndNotHasConfig()
    {
    	$exception = [
    		'message' => 'Service config not found',
    		'error_code' => 422,
    	];

    	$config = [];

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$requestJson
    		->shouldReceive('newGuzzle')
    		->never()
    		->withNoArgs()
    		->andReturnSelf()
    		->shouldReceive('getConfigValue')
    		->once()
    		->withNoArgs()
    		->andReturn($config);

    	$sendRequest = $requestJson->sendRequest('back', 'get', '/auth', ['Context' => 'test']);

    	$this->assertEquals($sendRequest, $exception);
    }

	/**
	 * @covers \RequestService\RequestJson::sendRequest
	 */
    public function testSendRequestException()
    {
    	$config = [
    		'back' => [
	    		'url' => 'localhost',
    		],
    	];

    	$header = [
    		'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Context' => 'test',
    		],
    	];

    	$body = ['json' => []];

    	$exception = [
    		'message' => ['response' => 'Missing Authorization'],
    		'error_code' => 401,
    	];

    	$clientExceptionMock = Mockery::mock(ClientException::class)
    		->shouldReceive('getResponse')
    		->twice()
    		->withNoArgs()
    		->andReturnSelf()
    		->shouldReceive('getBody')
    		->once()
    		->withNoArgs()
    		->andReturn(json_encode(['response' => 'Missing Authorization']))
    		->shouldReceive('getStatusCode')
    		->once()
    		->withNoArgs()
    		->andReturn(401)
    		->getMock();

    	$guzzleMock = Mockery::mock(Guzzle::class)
    		->shouldReceive('get')
    		->once()
    		->with('localhost/auth', array_merge($header, $body))
    		->andThrow($clientExceptionMock)
    		->shouldReceive('getBody')
    		->never()
    		->withNoArgs()
    		->andReturnSelf()
    		->getMock();

    	$requestJson = Mockery::mock(RequestJson::class, [$config])->makePartial();
    	$requestJson
    		->shouldReceive('newGuzzle')
    		->once()
    		->withNoArgs()
    		->andReturn($guzzleMock)
    		->shouldReceive('getConfigValue')
    		->once()
    		->withNoArgs()
    		->andReturn($config);

    	$sendRequest = $requestJson->sendRequest('back', 'get', '/auth', ['Context' => 'test']);

    	$this->assertEquals($sendRequest, $exception);
    }
}
