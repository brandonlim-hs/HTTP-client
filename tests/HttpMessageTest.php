<?php

namespace HttpClient\Tests;

use HttpClient\HttpMessage;
use HttpClient\Tests\Traits\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@link HttpMessage} class.
 *
 * @package HttpClient\Tests
 */
abstract class HttpMessageTest extends TestCase
{
    use DataProvider;

    /**
     * Create an object that extends {@link HttpMessage} for unit tests.
     *
     * @return mixed
     */
    abstract public function newHttpMessage();

    /**
     * Return a data provider array of valid body contents.
     *
     * @return array Return a data provider array of valid body contents.
     */
    public function validMessageBody()
    {
        $validBodyContent = [
            'Example body content', // String body content
            [ // Array body content
                'data' => 'Example body content'
            ]
        ];
        return $this->toDataProviderArray($validBodyContent);
    }

    /**
     * Test setting body in HTTP message.
     *
     * @dataProvider validMessageBody
     * @param string|array $body The body content.
     */
    public function testAddMessageBody($body)
    {
        $httpMessage = $this->newHttpMessage();
        $httpMessage->withBody($body);

        $this->assertEquals($body, $httpMessage->getBody());
    }

    /**
     * Test setting HTTP headers in HTTP message.
     */
    public function testHttpHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $httpMessage = $this->newHttpMessage();
        foreach ($headers as $headerName => $headerValue) {
            $httpMessage->withHeader($headerName, $headerValue);
        }
        $messageHeaders = $httpMessage->getHeaders();

        sort($headers);
        sort($messageHeaders);
        $this->assertEquals($headers, $messageHeaders);
    }
}