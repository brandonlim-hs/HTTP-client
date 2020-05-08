<?php

namespace HttpClient\Tests;

use HttpClient\Exceptions\HttpClientErrorException;
use HttpClient\Exceptions\HttpServerErrorException;
use HttpClient\HttpClient;
use HttpClient\HttpRequestMethod;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@link HttpClient} class.
 *
 * Uses Postman Echo to verify request and response data.
 *
 * @package HttpClient\Tests
 */
class HttpClientTest extends TestCase
{
    /**
     * Return a data provider array of valid HTTP requests.
     *
     * @return array Return a data provider array of valid HTTP requests.
     */
    public function validRequests()
    {
        return [
            [
                HttpRequestMethod::GET,
                'https://postman-echo.com/get?foo1=bar1&foo2=bar2',
                '',
                []
            ],
            [
                HttpRequestMethod::POST,
                'https://postman-echo.com/post',
                'This is expected to be sent back as part of response body.',
                [
                    'Content-type' => 'text/html; charset=UTF-8'
                ]
            ],
            [
                HttpRequestMethod::DELETE,
                'https://postman-echo.com/delete',
                '',
                []
            ],
        ];
    }

    /**
     * Test sending valid HTTP request.
     *
     * @dataProvider validRequests
     * @param string $method
     * @param string $url
     * @param string $body
     * @param array $headers
     */
    public function testSendValidHttpRequest(string $method, string $url, $body, array $headers)
    {
        $client = new HttpClient();
        $response = $client->send($method, $url, $body, $headers);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertNotEmpty($response->getBody());
        $this->assertNotEmpty($response->getHeaders());
    }

    /**
     * Return a data provider array of invalid HTTP requests.
     *
     * @return array Return a data provider array of invalid HTTP requests.
     */
    public function invalidRequests()
    {
        return [
            [
                HttpRequestMethod::GET,
                'https://postman-echo.com/status/404',
                '',
                [],
                HttpClientErrorException::class
            ],
            [
                HttpRequestMethod::GET,
                'https://postman-echo.com/status/500',
                '',
                [],
                HttpServerErrorException::class
            ],
        ];
    }

    /**
     * Test sending valid HTTP request should throw an {@link HttpClientErrorException} if status is 4xx or
     * {@link HttpServerErrorException} if status is 5xx.
     *
     * @dataProvider invalidRequests
     * @param string $method
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param string $expectedExceptionClass
     */
    public function testSendInvalidHttpRequest(
        string $method,
        string $url,
        $body,
        array $headers,
        string $expectedExceptionClass
    ) {
        $this->expectException($expectedExceptionClass);
        $client = new HttpClient();
        $client->send($method, $url, $body, $headers);
    }

    /**
     * Test sending HTTP request with HTTP header.
     */
    public function testHttpRequestHeader()
    {
        $client = new HttpClient();
        $headers = [
            'foo1' => 'bar1',
            'foo2' => 'bar2',
        ];
        $response = $client->send(
            HttpRequestMethod::GET,
            'https://postman-echo.com/headers',
            '',
            $headers
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $responseBody = $response->getBody();
        $responseBodyHeaders = $responseBody['headers'];
        foreach ($headers as $expectedHeaderName => $expectedHeaderValue) {
            $this->assertArrayHasKey($expectedHeaderName, $responseBodyHeaders);
            $this->assertEquals($expectedHeaderValue, $responseBodyHeaders[$expectedHeaderName]);
        }
        $this->assertNotEmpty($response->getHeaders());
    }

    /**
     * Test retrieving HTTP header from HTTP response.
     */
    public function testHttpResponseHeader()
    {
        $client = new HttpClient();
        $response = $client->send(
            HttpRequestMethod::GET,
            'https://postman-echo.com/response-headers?foo1=bar1&foo2=bar2',
            );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertNotEmpty($response->getBody());
        $responseHeaders = $response->getHeaders();
        foreach (
            [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ] as $expectedHeaderName => $expectedHeaderValue
        ) {
            $this->assertArrayHasKey($expectedHeaderName, $responseHeaders);
            $this->assertEquals($expectedHeaderValue, $responseHeaders[$expectedHeaderName]);
        }
    }

    /**
     * Test sending valid HTTP request with JSON payload.
     */
    public function testSendValidJsonHttpRequest()
    {
        $client = new HttpClient();
        $payload = [
            'foo1' => 'bar1',
            'foo2' => 'bar2',
        ];
        $response = $client->sendJson(
            HttpRequestMethod::POST,
            'https://postman-echo.com/post',
            $payload
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $responseBody = $response->getBody();
        $this->assertArrayHasKey('json', $responseBody);
        $this->assertEquals($payload, $responseBody['json']);
        $this->assertNotEmpty($response->getHeaders());
    }

    /**
     * Test retrieving chunked HTTP response.
     */
    public function testRetrieveChunkedHttpResponse()
    {
        $client = new HttpClient();
        $response = $client->send(
            HttpRequestMethod::GET,
            'https://postman-echo.com/stream/5'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertNotEmpty($response->getBody());
        $this->assertNotEmpty($response->getHeaders());
    }
}