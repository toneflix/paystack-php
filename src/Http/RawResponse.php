<?php

namespace Toneflix\Paystack\Http;

final class RawResponse
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $body,
    ) {}
}
