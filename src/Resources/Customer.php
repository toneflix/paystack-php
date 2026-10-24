<?php

namespace Toneflix\Paystack\Resources;

class Customer extends AbstractResource
{
    /**
     * Create a customer.
     *
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): object
    {
        return $this->client->request('POST', '/customer', $payload);
    }

    /**
     * Fetch a customer by email or customer code.
     */
    public function fetch(string $emailOrCode): object
    {
        return $this->client->request('GET', '/customer/'.rawurlencode($emailOrCode));
    }

    /**
     * Deactivate an existing authorization so it can no longer be charged.
     */
    public function deactivateAuthorization(string $authorizationCode): object
    {
        return $this->client->request('POST', '/customer/deactivate_authorization', [
            'authorization_code' => $authorizationCode,
        ]);
    }
}
