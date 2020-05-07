<?php

namespace HttpClient\Tests;

use HttpClient\HttpClient;
use HttpClient\HttpRequestMethod;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@link HttpClient} class.
 *
 * Uses Postman Echo to verify sent request data.
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
        $this->assertNotEmpty($response);
    }

    /**
     * Test sending valid HTTP request with JSON payload.
     */
    public function testSendValidJsonHttpRequest()
    {
        $client = new HttpClient();
        $response = $client->sendJson(
            HttpRequestMethod::POST,
            'https://postman-echo.com/post',
            [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        );
        $this->assertNotEmpty($response);
    }
}