<?php

namespace HttpClient;

/**
 * HttpResponse represents an incoming response from the server.
 *
 * @package HttpClient
 */
class HttpResponse extends HttpMessage
{
    /**
     * @var string The response reason phrase associated with the status code.
     */
    protected $reasonPhrase;

    /**
     * @var int The 3-digit integer response status code.
     */
    protected $statusCode;

    /**
     * Return the reason phrase of the response.
     *
     * @return string Return the reason phrase.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Set the reason phrase for the response.
     *
     * @param string $reasonPhrase The new reason phrase.
     * @return $this
     */
    public function withReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    /**
     * Return the status code of the response.
     *
     * @return string Return the status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the status code for the response.
     *
     * @param string $statusCode The new status code.
     * @return $this
     */
    public function withStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}