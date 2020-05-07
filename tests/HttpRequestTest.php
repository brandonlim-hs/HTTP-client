<?php

namespace HttpClient\Tests;

use HttpClient\HttpRequest;
use HttpClient\HttpRequestMethod;
use HttpClient\Tests\Traits\DataProvider;
use InvalidArgumentException;

/**
 * Unit tests for {@link HttpRequest} class.
 *
 * @package HttpClient\Tests
 */
class HttpRequestTest extends HttpMessageTest
{
    use DataProvider;

    /**
     * @inheritDoc
     */
    public function newHttpMessage()
    {
        return new HttpRequest();
    }

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
     * Return a data provider array of valid URLs.
     *
     * @return array Return a data provider array of valid URLs.
     */
    public function validUrl()
    {
        $validUrls = [
            'https://www.google.com.au/',
            'www.php.net/downloads',
        ];
        return $this->toDataProviderArray($validUrls);
    }

    /**
     * Test setting a valid URL for a request.
     *
     * @dataProvider validUrl
     * @param string $url The URL.
     */
    public function testSetValidUrl($url)
    {
        $request = new HttpRequest();
        $request->withUrl($url);

        $this->assertEquals($url, $request->getUrl());
    }

    /**
     * Return a data provider array of invalid URLs.
     *
     * @return array Return a data provider array of invalid URLs.
     */
    public function invalidUrl()
    {
        $invalidUrls = [
            '///',
        ];
        return $this->toDataProviderArray($invalidUrls);
    }

    /**
     * Test setting an invalid URL for a request should throw an {@link InvalidArgumentException}.
     *
     * @dataProvider invalidUrl
     * @param string $url The URL.
     */
    public function testSetInvalidUrl($url)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new HttpRequest();
        $request->withUrl($url);
    }
}