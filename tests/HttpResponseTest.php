<?php

namespace HttpClient\Tests;

use HttpClient\HttpResponse;

/**
 * Unit tests for {@link HttpResponse} class.
 *
 * @package HttpClient\Tests
 */
class HttpResponseTest extends HttpMessageTest
{
    /**
     * @inheritDoc
     */
    public function newHttpMessage()
    {
        return new HttpResponse();
    }

    /**
     * Test setting reason phrase for a response.
     */
    public function testSetReasonPhrase()
    {
        $reasonPhrase = 'OK';
        $response = new HttpResponse();
        $response->withReasonPhrase($reasonPhrase);

        $this->assertEquals($reasonPhrase, $response->getReasonPhrase());
    }

    /**
     * Test setting status code for a response.
     */
    public function testSetStatusCode()
    {
        $statusCode = 200;
        $response = new HttpResponse();
        $response->withStatusCode($statusCode);

        $this->assertEquals($statusCode, $response->getStatusCode());
    }
}