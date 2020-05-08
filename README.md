# HTTP Client

A light weight HTTP client.

## Example

```php
use HttpClient\HttpClient;
use HttpClient\HttpRequestMethod;

$client = new HttpClient();

// Send GET request
$response = $client->send(HttpRequestMethod::GET, 'https://postman-echo.com/get?foo1=bar1&foo2=bar2');

// Post JSON request
$response = $client->send(
    HttpRequestMethod::POST,
    'https://postman-echo.com/post',
    [
        'foo1' => 'bar1',
        'foo2' => 'bar2',
    ]
);
```