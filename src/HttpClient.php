<?php

namespace HttpClient;

use RuntimeException;

/**
 * HttpClient is responsible for sending {@link HttpRequest}.
 *
 * @package HttpClient
 */
class HttpClient
{
    /**
     * Send HTTP request.
     *
     * @param string $method The HTTP method for the request.
     * @param string $url The URL for the request.
     * @param string $body The body of the request.
     * @param array $headers The headers for the request.
     * @return mixed
     */
    public function send(string $method, string $url, string $body = '', array $headers = [])
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
     * @return mixed
     */
    public function sendJson(string $method, string $url, array $body = [], array $headers = [])
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
     * @return mixed
     * @throws RuntimeException if unable to establish socket connection.
     */
    private function sendUsingFsockopen(HttpRequest $request)
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
        fputs($fp, "$method $path$query $protocol\r\n");
        fputs($fp, "Host: $host\r\n");
        foreach ($request->getHeaders() as $headerName => $headerValue) {
            fputs($fp, "$headerName: $headerValue\r\n");
        }
        fputs($fp, "Content-length: " . strlen($body) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $body . "\r\n\r\n");

        $content = stream_get_contents($fp);
        fclose($fp);

        return $content;
    }
}