<?php

namespace HttpClient;

use ReflectionClass;
use ReflectionException;

/**
 * HttpRequestMethod defines an array of supported HTTP request methods.
 *
 * HttpRequestMethod defines an array of supported HTTP request methods, sourced
 * from https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods.
 * Ideally this class should be an enum, i.e. by extending myclabs/php-enum.
 *
 * @package HttpClient
 */
class HttpRequestMethod
{
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const CONNECT = 'CONNECT';
    public const OPTIONS = 'OPTIONS';
    public const TRACE = 'TRACE';
    public const PATCH = 'PATCH';

    /**
     * Return an array of all supported HTTP request methods.
     *
     * @return array Return an array of all supported HTTP request methods.
     */
    public static function getAllMethods()
    {
        try {
            $reflectionClass = new ReflectionClass(static::class);
            return array_values($reflectionClass->getConstants());
        } catch (ReflectionException $exception) {
            return [];
        }
    }
}