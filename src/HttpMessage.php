<?php

namespace HttpClient;

/**
 * HttpMessage represents how data is exchanged between a server and a client.
 *
 * There are two types of messages: {@link HttpRequest} sent by the client to trigger an action on the server,
 * and {@link HttpResponse}, the answer from the server. https://developer.mozilla.org/en-US/docs/Web/HTTP/Messages
 *
 * @package HttpClient
 */
abstract class HttpMessage
{
    /**
     * @var string|array The body of the message.
     */
    protected $body = '';

    /**
     * @var array HTTP headers to be sent with the message.
     */
    protected $headers = [];

    /**
     * @var string HTTP protocol, currently only HTTP/1.1 is supported.
     */
    protected $protocol = 'HTTP/1.1';

    /**
     * Return the body of the message.
     *
     * @return string|array Returns the body content.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body of the message.
     *
     * @param string|array $body The new body content.
     * @return $this
     */
    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Return the HTTP headers of the message.
     *
     * @return array Return the HTTP headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Add a HTTP header to be sent with the message.
     *
     * @param string $name The header field name (case-insensitive).
     * @param string $value The new value for the given header field name.
     * @return $this
     */
    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Return the HTTP protocol of the message.
     *
     * @return string Return the HTTP protocol.
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}