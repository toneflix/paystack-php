<?php

namespace Toneflix\Paystack\Resources;

class TransferRecipient extends AbstractResource
{
    /**
     * Create a transfer recipient.
     *
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): object
    {
        return $this->client->request('POST', '/transferrecipient', $payload);
    }
}
