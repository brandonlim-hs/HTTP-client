<?php

namespace HttpClient\Tests;

use HttpClient\HttpResponse;

/**
 * Unit tests for {@link HttpResponse} class.
 *
 * @package HttpClient\Tests
 */
class HttpResponseTest extends HttpMessageTest
{
    /**
     * @inheritDoc
     */
    public function newHttpMessage()
    {
        return new HttpResponse();
    }
}