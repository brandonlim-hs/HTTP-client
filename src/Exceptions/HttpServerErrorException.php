<?php

namespace HttpClient\Exceptions;

/**
 * Exception thrown if a HTTP response indicates a 5xx status code.
 *
 * @package HttpClient\Exceptions
 */
class HttpServerErrorException extends \RuntimeException
{
}