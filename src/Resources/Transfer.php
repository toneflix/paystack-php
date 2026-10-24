<?php

namespace Toneflix\Paystack\Resources;

class Transfer extends AbstractResource
{
    /**
     * Initiate a transfer.
     *
     * @param  array<string, mixed>  $payload
     */
    public function initiate(array $payload): object
    {
        return $this->client->request('POST', '/transfer', $payload);
    }
}
