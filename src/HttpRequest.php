<?php

namespace HttpClient;

use InvalidArgumentException;

/**
 * HttpRequest represents an outgoing HTTP request.
 *
 * @package HttpClient
 */
class HttpRequest
{
    /**
     * @var string|array The body of the request.
     */
    protected $body = '';

    /**
     * @var array HTTP headers to be sent with the request.
     */
    protected $headers = [];

    /**
     * @var string HTTP method for the request.
     */
    protected $method = HttpRequestMethod::GET;

    /**
     * @var string HTTP protocol, currently only HTTP/1.1 is supported.
     */
    protected $protocol = 'HTTP/1.1';

    /**
     * @var string The URL of the request.
     */
    protected $url = '';

    /**
     * Return the body of the request.
     *
     * @return string|array Returns the request body.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body of the request.
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
     * Return the HTTP headers of the request.
     *
     * @return array Return the request headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Add a HTTP header to be sent with the request.
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
     * Return the HTTP method of the request.
     *
     * @return string Return the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the HTTP method for the request.
     *
     * @param string $method The new method (case-sensitive).
     * @return $this
     * @throws InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if (!in_array($method, HttpRequestMethod::getAllMethods(), true)) {
            throw new InvalidArgumentException("Invalid HTTP method given: {$method}");
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Return the HTTP protocol of the request.
     *
     * @return string Return the request protocol.
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Return the URL of the request.
     *
     * @return string Return the request URL.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL for the request.
     *
     * @param string $url The new URL (case-insensitive).
     * @return $this
     * @throws InvalidArgumentException for invalid URLs.
     */
    public function withUrl($url)
    {
        $url = strtolower($url);
        if (!parse_url($url)) {
            // URL is malformed
            throw new InvalidArgumentException("Invalid URL given: {$url}");
        }
        $this->url = $url;

        return $this;
    }
}