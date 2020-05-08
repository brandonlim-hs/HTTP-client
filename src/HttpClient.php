<?php

namespace HttpClient;

use HttpClient\Exceptions\HttpClientErrorException;
use HttpClient\Exceptions\HttpServerErrorException;
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
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
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

        $content = stream_get_contents($fp);
        fclose($fp);

        return $this->getHttpResponseFromString($content);
    }

    /**
     * Return {@link HttpResponse} representing the HTTP message string.
     *
     * @param string $message The HTTP message string.
     * @return HttpResponse The HTTP response.
     */
    private function getHttpResponseFromString(string $message): HttpResponse
    {
        $response = new HttpResponse();

        $lines = explode(self::NEW_LINE, $message);

        $statusLine = array_shift($lines);
        [$protocol, $status, $reason] = explode(' ', $statusLine);
        if (substr($status, 0, 1) === '4') {
            // Throw exception for 4xx status code
            throw new HttpClientErrorException("Server responded with $status status code.");
        } elseif (substr($status, 0, 1) === '5') {
            // Throw exception for 5xx status code
            throw new HttpServerErrorException("Server responded with $status status code.");
        }
        $response->withStatusCode($status)->withReasonPhrase($reason);

        $bodySection = false;
        $body = '';
        foreach ($lines as $line) {
            if (empty($line)) {
                // Found empty line, next line is body section
                $bodySection = true;
                continue;
            }
            if ($bodySection) {
                // Combine all body lines
                $body .= $line;
            } else {
                // Header name and value is separated by ': '
                [$headerName, $headerValue] = explode(': ', $line);
                $response->withHeader($headerName, $headerValue);
            }
        }
        $response->withBody($body);

        return $response;
    }
}