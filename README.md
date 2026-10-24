# Toneflix Paystack PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/toneflix/paystack-php.svg?style=flat-square)](https://packagist.org/packages/toneflix/paystack-php)
[![Tests](https://github.com/toneflix/paystack-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/toneflix/paystack-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/toneflix/paystack-php.svg?style=flat-square)](https://packagist.org/packages/toneflix/paystack-php)

## Introduction

A small, dependency-light PHP client for the [Paystack](https://paystack.com) API. It covers
transactions, transfers, transfer recipients and customer authorization management, and is
built against PHP 8.3/8.4 with no required HTTP client dependency (cURL is used directly, and
the transport is swappable for testing).

## Installation

```bash
composer require toneflix/paystack-php
```

## Usage

```php
use Toneflix\Paystack\Paystack;

$paystack = new Paystack('sk_test_xxx');

// Initialize a transaction
$tranx = $paystack->transaction->initialize([
    'amount' => 500000, // in kobo
    'email' => 'customer@example.com',
    'reference' => 'TRX-123',
]);

// Verify a transaction
$verified = $paystack->transaction->verify(['reference' => 'TRX-123']);

// Deactivate a customer's saved authorization
$paystack->customer->deactivateAuthorization('AUTH_abc123');

// Create a transfer recipient and initiate a transfer
$recipient = $paystack->transferrecipient->create([
    'type' => 'nuban',
    'name' => 'Jane Doe',
    'account_number' => '0123456789',
    'bank_code' => '058',
    'currency' => 'NGN',
]);

$transfer = $paystack->transfer->initiate([
    'source' => 'balance',
    'amount' => 500000,
    'recipient' => $recipient->data->recipient_code,
    'reference' => 'TRX-124',
]);
```

### Building checkout metadata

`MetadataBuilder` mirrors Paystack's `metadata` object: `withCustomField()` appends an entry to
`custom_fields`, and any other `withX()` call sets the matching snake_case key (e.g.
`withCancelAction($url)` sets `cancel_action`).

```php
use Toneflix\Paystack\MetadataBuilder;

$metadata = (new MetadataBuilder())
    ->withCustomField('Name', 'Jane Doe')
    ->withCancelAction('https://example.com/cancel')
    ->build();

$paystack->transaction->initialize([
    'amount' => 500000,
    'email' => 'customer@example.com',
    'metadata' => $metadata,
]);
```

### Error handling

A failed API call throws `Toneflix\Paystack\Exception\ApiException`, whose `getResponseObject()`
returns the decoded Paystack response (when one was received) for inspection.

```php
use Toneflix\Paystack\Exception\ApiException;

try {
    $paystack->transaction->initialize(['amount' => -1]);
} catch (ApiException $e) {
    report($e->getMessage());
    report($e->getResponseObject());
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Toneflix Code](https://github.com/toneflix)
-   [Legacy](https://github.com/3m1n3nc3)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
