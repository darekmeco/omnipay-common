<?php

namespace Omnipay\Common\Http;

use Mockery as m;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Omnipay\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testEmptyConstruct()
    {
        $client = new Client();

        $this->assertAttributeInstanceOf(HttpClient::class, 'httpClient', $client);
        $this->assertAttributeInstanceOf(RequestFactory::class, 'requestFactory', $client);
    }

    public function testCreateRequest()
    {
         $client = $this->getHttpClient();

         $request = $client->createRequest('GET', '/path', ['foo' => 'bar']);

         $this->assertInstanceOf(Request::class, $request);
         $this->assertEquals('/path', $request->getUri());
         $this->assertEquals(['bar'], $request->getHeader('foo'));
    }


    public function testSend()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
            [],
            null,
            '1.1',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')->with($request)->once();

        $client->send('GET', '/path');
    }

    public function testSendParsesArrayBody()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('POST', '/path', [], 'a=1&b=2');

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'POST',
            '/path',
            [],
            'a=1&b=2',
            '1.1',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')->with($request)->once();

        $client->send('POST', '/path', [], ['a'=>'1', 'b'=>2]);
    }


    public function testGet()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
            [],
            null,
            '1.1',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')->with($request)->once();

        $client->get('/path');
    }

    public function testPost()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('POST', '/path', [], 'a=b');

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'POST',
            '/path',
            [],
            'a=b',
            '1.1',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')->with($request)->once();

        $client->post('/path', [], ['a' => 'b']);
    }
}