<?php

namespace HttpClient\Tests;

use HttpClient\HttpRequest;
use HttpClient\HttpRequestMethod;
use HttpClient\Tests\Traits\DataProvider;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@link HttpRequest} class.
 *
 * @package HttpClient\Tests
 */
class HttpRequestTest extends TestCase
{
    use DataProvider;

    /**
     * Return a data provider array of valid HTTP methods.
     *
     * @return array Return a data provider array of valid HTTP methods.
     */
    public function validHttpMethods()
    {
        $validHttpMethods = HttpRequestMethod::getAllMethods();
        return $this->toDataProviderArray($validHttpMethods);
    }

    /**
     * Test setting a valid HTTP method for a request.
     *
     * @dataProvider validHttpMethods
     * @param string $method The HTTP method.
     */
    public function testValidHttpMethods($method)
    {
        $request = new HttpRequest();
        $request->withMethod($method);

        $this->assertEquals($method, $request->getMethod());
    }

    /**
     * Return a data provider array of invalid HTTP methods.
     *
     * @return array Return a data provider array of invalid HTTP methods.
     */
    public function invalidHttpMethods()
    {
        $invalidHttpMethods = [
            strtolower(HttpRequestMethod::GET), // Method is case-sensitive
            'INVALID'
        ];
        return $this->toDataProviderArray($invalidHttpMethods);
    }

    /**
     * Test setting an invalid HTTP method for a request should throw an {@link InvalidArgumentException}.
     *
     * @dataProvider invalidHttpMethods
     * @param $method
     */
    public function testInvalidHttpMethod($method)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new HttpRequest();
        $request->withMethod($method);
    }

    /**
     * Test setting headers in HTTP request.
     */
    public function testHttpHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $request = new HttpRequest();
        foreach ($headers as $headerName => $headerValue) {
            $request->withHeader($headerName, $headerValue);
        }
        $requestHeaders = $request->getHeaders();

        sort($headers);
        sort($requestHeaders);
        $this->assertEquals($headers, $requestHeaders);
    }
}