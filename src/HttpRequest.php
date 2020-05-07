<?php

namespace HttpClient;

use InvalidArgumentException;

/**
 * HttpRequest represents an outgoing HTTP request from the client to the server.
 *
 * @package HttpClient
 */
class HttpRequest extends HttpMessage
{
    /**
     * @var string HTTP method for the request.
     */
    protected $method = HttpRequestMethod::GET;

    /**
     * @var string The URL of the request.
     */
    protected $url = '';

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