<?php

namespace Toneflix\Paystack\Tests;

use Toneflix\Paystack\Contracts\TransportInterface;
use Toneflix\Paystack\Http\RawResponse;

final class FakeTransport implements TransportInterface
{
    /** @var array<int, array{method: string, url: string, headers: array<string, string>, body: ?string}> */
    public array $requests = [];

    public function __construct(private readonly RawResponse $response)
    {
    }

    public function send(string $method, string $url, array $headers, ?string $body): RawResponse
    {
        $this->requests[] = compact('method', 'url', 'headers', 'body');

        return $this->response;
    }
}
