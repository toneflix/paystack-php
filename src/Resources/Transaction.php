<?php

namespace Toneflix\Paystack\Resources;

class Transaction extends AbstractResource
{
    /**
     * Initialize a transaction.
     *
     * @param  array<string, mixed>  $payload
     */
    public function initialize(array $payload): object
    {
        return $this->client->request('POST', '/transaction/initialize', $payload);
    }

    /**
     * Verify a transaction by its reference.
     *
     * @param  array{reference: string}  $payload
     */
    public function verify(array $payload): object
    {
        $reference = $payload['reference'] ?? throw new \InvalidArgumentException('A transaction reference is required.');

        return $this->client->request('GET', '/transaction/verify/'.rawurlencode($reference));
    }
}
