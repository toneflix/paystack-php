<?php

namespace Toneflix\Paystack\Contracts;

use Toneflix\Paystack\Http\RawResponse;

interface TransportInterface
{
    /**
     * Send an HTTP request and return the raw response.
     *
     * @param  array<string, string>  $headers
     */
    public function send(string $method, string $url, array $headers, ?string $body): RawResponse;
}
