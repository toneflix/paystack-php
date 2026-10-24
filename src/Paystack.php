<?php

namespace Toneflix\Paystack;

use Toneflix\Paystack\Contracts\TransportInterface;
use Toneflix\Paystack\Exception\ApiException;
use Toneflix\Paystack\Http\CurlTransport;
use Toneflix\Paystack\Resources\Customer;
use Toneflix\Paystack\Resources\Transaction;
use Toneflix\Paystack\Resources\Transfer;
use Toneflix\Paystack\Resources\TransferRecipient;

/**
 * @property-read Transaction $transaction
 * @property-read Transfer $transfer
 * @property-read TransferRecipient $transferrecipient
 * @property-read Customer $customer
 */
class Paystack
{
    public const VERSION = '1.0.0';

    public const BASE_URL = 'https://api.paystack.co';

    /** @var array<string, object> */
    private array $resources = [];

    private readonly TransportInterface $transport;

    public function __construct(
        private readonly string $secretKey,
        ?TransportInterface $transport = null,
        private readonly string $baseUrl = self::BASE_URL,
    ) {
        if ($this->secretKey === '' || ! str_starts_with($this->secretKey, 'sk_')) {
            throw new \InvalidArgumentException('A valid Paystack secret key must start with "sk_".');
        }

        $this->transport = $transport ?? new CurlTransport();
    }

    public function __get(string $name): object
    {
        return $this->resources[$name] ??= match ($name) {
            'transaction' => new Transaction($this),
            'transfer' => new Transfer($this),
            'transferrecipient' => new TransferRecipient($this),
            'customer' => new Customer($this),
            default => throw new \InvalidArgumentException(sprintf('Unknown Paystack resource "%s".', $name)),
        };
    }

    /**
     * Send a request to the Paystack API and return the decoded JSON body.
     *
     * @param  array<string, mixed>  $payload
     */
    public function request(string $method, string $endpoint, array $payload = []): object
    {
        $url = $this->baseUrl.$endpoint;
        $body = null;

        if ($method === 'GET') {
            if ($payload !== []) {
                $url .= '?'.http_build_query($payload);
            }
        } else {
            $body = json_encode($payload);
        }

        $raw = $this->transport->send($method, $url, [
            'Authorization' => 'Bearer '.$this->secretKey,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Toneflix-Paystack/'.self::VERSION,
        ], $body);

        $decoded = json_decode($raw->body);

        if (! is_object($decoded) || ! property_exists($decoded, 'status') || ! $decoded->status) {
            $message = is_object($decoded) && isset($decoded->message)
                ? $decoded->message
                : sprintf('Paystack request failed with status %d.', $raw->statusCode);

            throw new ApiException($message, $raw->statusCode, is_object($decoded) ? $decoded : null);
        }

        return $decoded;
    }
}
