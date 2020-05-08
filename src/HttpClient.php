<?php

namespace HttpClient;

use HttpClient\Exceptions\HttpClientErrorException;
use HttpClient\Exceptions\HttpServerErrorException;
use HttpClient\Exceptions\JsonConversionException;
use RuntimeException;

/**
 * HttpClient is responsible for sending {@link HttpRequest}.
 *
 * @package HttpClient
 */
class HttpClient
{
    /**
     * Constant for new line.
     */
    private const NEW_LINE = "\r\n";

    /**
     * The MIME media type for JSON text.
     */
    const APPLICATION_JSON = 'application/json';

    /**
     * Send HTTP request.
     *
     * @param string $method The HTTP method for the request.
     * @param string $url The URL for the request.
     * @param string $body The body of the request.
     * @param array $headers The headers for the request.
     * @return HttpResponse
     */
    public function send(string $method, string $url, string $body = '', array $headers = []): HttpResponse
    {
        $request = (new HttpRequest())
            ->withMethod($method)
            ->withUrl($url)
            ->withBody($body);
        foreach ($headers as $headerName => $headerValue) {
            $request->withHeader($headerName, $headerValue);
        }

        return $this->sendUsingFsockopen($request);
    }

    /**
     * Send HTTP request with JSON payload.
     *
     * @param string $method The HTTP method for the request.
     * @param string $url The URL for the request.
     * @param array $body The JSON body of the request.
     * @param array $headers The headers for the request.
     * @return HttpResponse
     */
    public function sendJson(string $method, string $url, array $body = [], array $headers = []): HttpResponse
    {
        $body = json_encode($body);
        $headers = array_merge(
            $headers,
            [
                'Content-type' => self::APPLICATION_JSON,
                'Accept' => self::APPLICATION_JSON,
            ]
        );

        return $this->send($method, $url, $body, $headers);
    }

    /**
     * Send the given HTTP request using fsockopen.
     *
     * @param HttpRequest $request The HTTP request.
     * @return HttpResponse The HTTP response.
     * @throws RuntimeException if unable to establish socket connection.
     */
    private function sendUsingFsockopen(HttpRequest $request): HttpResponse
    {
        $method = $request->getMethod();
        $protocol = $request->getProtocol();
        $body = $request->getBody();
        if (is_array($body)) {
            $body = json_encode($body);
        }

        // Extract URL components
        $parsed_url = parse_url($request->getUrl());
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : 80;
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';

        // Use SSL if scheme is HTTPS
        if ($scheme === 'https') {
            $scheme = 'ssl';
            $port = 443;
        }

        $hostname = sprintf('%s%s', $scheme ? $scheme . '://' : '', $host);

        // Open Internet or Unix domain socket connection
        $fp = fsockopen($hostname, $port, $errno, $errstr, 30);
        if (!$fp) {
            throw new RuntimeException($errstr);
        }

        // Send the server request
        fputs($fp, "$method $path$query $protocol" . self::NEW_LINE);
        fputs($fp, "Host: $host" . self::NEW_LINE);
        foreach ($request->getHeaders() as $headerName => $headerValue) {
            fputs($fp, "$headerName: $headerValue" . self::NEW_LINE);
        }
        fputs($fp, "Content-length: " . strlen($body) . self::NEW_LINE);
        fputs($fp, "Connection: close" . self::NEW_LINE . self::NEW_LINE);
        fputs($fp, $body . self::NEW_LINE . self::NEW_LINE);

        $response = $this->getHttpResponseFromFp($fp);

        fclose($fp);

        return $response;
    }

    /**
     * Return {@link HttpResponse} representing the HTTP message string.
     *
     * @param resource $fp The file pointer to socket.
     * @return HttpResponse The HTTP response.
     */
    private function getHttpResponseFromFp($fp): HttpResponse
    {
        $response = new HttpResponse();

        // Get status line
        $statusLine = trim(fgets($fp));
        [$protocol, $status, $reason] = explode(' ', $statusLine);
        if (substr($status, 0, 1) === '4') {
            // Throw exception for 4xx status code
            throw new HttpClientErrorException("Server responded with $status status code.");
        } elseif (substr($status, 0, 1) === '5') {
            // Throw exception for 5xx status code
            throw new HttpServerErrorException("Server responded with $status status code.");
        }
        $response->withStatusCode($status)->withReasonPhrase($reason);

        // Get headers
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            if ($line === '') {
                // Found empty line, next line is body section
                break;
            } else {
                // Header name and value is separated by ': '
                [$headerName, $headerValue] = explode(': ', $line);
                $response->withHeader($headerName, $headerValue);
            }
        }

        // Get body
        $body = $this->getBodyContentFromFp($fp, $response->getHeader('Transfer-Encoding') === 'chunked');
        if (strpos($response->getHeader('Content-Type'), self::APPLICATION_JSON) !== false) {
            $body = json_decode($body, true);
            if ($body === null) {
                // Throw exception if unable to decode JSON string
                throw new JsonConversionException('Error decoding JSON:' . self::NEW_LINE . $body);
            }
        }
        $response->withBody($body);

        return $response;
    }

    /**
     * Return the body content as a string.
     *
     * @param resource $fp The file pointer to socket.
     * @param bool $chunked The boolean flag whether the body is chunked.
     * @return string
     */
    private function getBodyContentFromFp($fp, bool $chunked = false): string
    {
        $body = '';
        $chunk_length = null;
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            if ($chunked) {
                if ($chunk_length === null) {
                    // Convert length to decimal
                    $chunk_length = hexdec($line);
                    // No more to read if length is 0
                    if ($chunk_length === 0) {
                        break;
                    }
                    continue;
                }
                $chunk_length -= strlen($line);
                // Reset and get new chunk length when finish reading current chunk
                if ($chunk_length <= 0) {
                    $chunk_length = null;
                }
            }
            // Combine all body lines
            $body .= $line;
        }

        return $body;
    }
}