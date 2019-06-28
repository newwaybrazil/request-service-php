<?php

namespace RequestService;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestJsonTest extends TestCase
{
	/**
	 * @covers \RequestService\RequestJson::prepareBody
	 */
    public function testPrepareBody()
    {
    	$body = [
    		'teste' => true,
    	];

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareBody = $requestJson->prepareBody($body);

    	$this->assertEquals($prepareBody, ['json' => $body]);
    }

    /**
     * @covers \RequestService\RequestJson::prepareBody
     */
    public function testPrepareBodyAndNotSendValue()
    {
    	$body = [];

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareBody = $requestJson->prepareBody($body);

    	$this->assertEquals($prepareBody, $body);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareUrl
	 */
    public function testPrepareUrlAndUriWithPipe()
    {
    	$url = 'localhost/';
    	$uri = '/auth';
    	$result = 'localhost/auth';

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareUrl = $requestJson->prepareUrl($url, $uri);

    	$this->assertEquals($prepareUrl, $result);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareUrl
	 */
    public function testPrepareUrlWithPipeAndUriNotHasPipe()
    {
    	$url = 'localhost/';
    	$uri = 'auth';
    	$result = 'localhost/auth';

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareUrl = $requestJson->prepareUrl($url, $uri);

    	$this->assertEquals($prepareUrl, $result);
    }

    /**
     * @covers \RequestService\RequestJson::prepareUrl
     */
    public function testPrepareUrlWithPipeAndUriHasSubRoute()
    {
        $url = 'localhost/';
        $uri = 'auth/generate';
        $result = 'localhost/auth/generate';

        $requestJson = Mockery::mock(RequestJson::class)->makePartial();
        $prepareUrl = $requestJson->prepareUrl($url, $uri);

        $this->assertEquals($prepareUrl, $result);
    }

    /**
     * @covers \RequestService\RequestJson::prepareUrl
     */
    public function testPrepareUrlWithPipeAndUriHasMoreThanOnePipe()
    {
        $url = 'localhost/';
        $uri = '/auth/generate';
        $result = 'localhost/auth/generate';

        $requestJson = Mockery::mock(RequestJson::class)->makePartial();
        $prepareUrl = $requestJson->prepareUrl($url, $uri);

        $this->assertEquals($prepareUrl, $result);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareUrl
	 */
    public function testPrepareUrlNotHasPipeAndUriHasPipe()
    {
    	$url = 'localhost';
    	$uri = '/auth';
    	$result = 'localhost/auth';

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareUrl = $requestJson->prepareUrl($url, $uri);

    	$this->assertEquals($prepareUrl, $result);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareUrl
	 */
    public function testPrepareUrlWithProtocol()
    {
    	$url = 'http://localhost/';
    	$uri = '/auth';
    	$result = 'http://localhost/auth';

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareUrl = $requestJson->prepareUrl($url, $uri);

    	$this->assertEquals($prepareUrl, $result);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareUrl
	 */
    public function testPrepareUrlWithSecurityProtocol()
    {
    	$url = 'https://localhost/';
    	$uri = '/auth';
    	$result = 'https://localhost/auth';

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareUrl = $requestJson->prepareUrl($url, $uri);

    	$this->assertEquals($prepareUrl, $result);
    }

	/**
	 * @covers \RequestService\RequestJson::prepareHeader
	 */
    public function testPrepareHeader()
    {
    	$header = [
    		'Context' => 'teste',
    	];

    	$result = [
    		'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Context' => 'teste',
			],
    	];

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
    	$prepareHeader = $requestJson->prepareHeader($header);

    	$this->assertEquals($prepareHeader, $result);
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

    	$body = [];

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

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
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

    	$body = [];

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

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
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

    	$body = [];

    	$exception = [
    		'response' => 'Missing Authorization',
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

    	$requestJson = Mockery::mock(RequestJson::class)->makePartial();
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
