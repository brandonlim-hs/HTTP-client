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
     * @var string HTTP method for the request.
     */
    protected $method = HttpRequestMethod::GET;

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
}