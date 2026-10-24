<?php

namespace Toneflix\Paystack\Http;

use Toneflix\Paystack\Contracts\TransportInterface;
use Toneflix\Paystack\Exception\ApiException;

final class CurlTransport implements TransportInterface
{
    public function send(string $method, string $url, array $headers, ?string $body): RawResponse
    {
        $ch = curl_init($url);

        $flatHeaders = [];
        foreach ($headers as $name => $value) {
            $flatHeaders[] = "{$name}: {$value}";
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $flatHeaders,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new ApiException("Paystack request failed: {$error}");
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new RawResponse($statusCode, $responseBody);
    }
}
