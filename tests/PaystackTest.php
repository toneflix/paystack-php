<?php

namespace Toneflix\Paystack\Tests;

use PHPUnit\Framework\TestCase;
use Toneflix\Paystack\Exception\ApiException;
use Toneflix\Paystack\Http\RawResponse;
use Toneflix\Paystack\Paystack;

class PaystackTest extends TestCase
{
    public function testConstructorRejectsAnInvalidSecretKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Paystack('not-a-secret-key');
    }

    public function testTransactionInitializeSendsAPostRequest(): void
    {
        $transport = new FakeTransport(new RawResponse(200, json_encode([
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => ['reference' => 'TRX-123'],
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);

        $result = $paystack->transaction->initialize(['amount' => 1000, 'email' => 'jane@example.com']);

        $this->assertSame('TRX-123', $result->data->reference);
        $this->assertCount(1, $transport->requests);
        $this->assertSame('POST', $transport->requests[0]['method']);
        $this->assertSame('https://api.paystack.co/transaction/initialize', $transport->requests[0]['url']);
        $this->assertSame(
            ['amount' => 1000, 'email' => 'jane@example.com'],
            json_decode($transport->requests[0]['body'], true)
        );
    }

    public function testTransactionVerifySendsAGetRequestWithTheReferenceInThePath(): void
    {
        $transport = new FakeTransport(new RawResponse(200, json_encode([
            'status' => true,
            'message' => 'Verification successful',
            'data' => ['status' => 'success'],
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);

        $result = $paystack->transaction->verify(['reference' => 'TRX-123']);

        $this->assertSame('success', $result->data->status);
        $this->assertSame('GET', $transport->requests[0]['method']);
        $this->assertSame('https://api.paystack.co/transaction/verify/TRX-123', $transport->requests[0]['url']);
    }

    public function testTransactionVerifyRequiresAReference(): void
    {
        $paystack = new Paystack('sk_test_abc', new FakeTransport(new RawResponse(200, '{}')));

        $this->expectException(\InvalidArgumentException::class);

        $paystack->transaction->verify([]);
    }

    public function testCustomerDeactivateAuthorizationSendsTheAuthorizationCode(): void
    {
        $transport = new FakeTransport(new RawResponse(200, json_encode([
            'status' => true,
            'message' => 'Authorization has been deactivated',
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);
        $paystack->customer->deactivateAuthorization('AUTH_abc123');

        $this->assertSame(
            ['authorization_code' => 'AUTH_abc123'],
            json_decode($transport->requests[0]['body'], true)
        );
        $this->assertSame('https://api.paystack.co/customer/deactivate_authorization', $transport->requests[0]['url']);
    }

    public function testTransferRecipientCreateSendsAPostRequest(): void
    {
        $transport = new FakeTransport(new RawResponse(200, json_encode([
            'status' => true,
            'message' => 'Recipient created',
            'data' => ['recipient_code' => 'RCP_123'],
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);

        $result = $paystack->transferrecipient->create(['type' => 'nuban', 'name' => 'Jane Doe']);

        $this->assertSame('RCP_123', $result->data->recipient_code);
        $this->assertSame('https://api.paystack.co/transferrecipient', $transport->requests[0]['url']);
    }

    public function testTransferInitiateSendsAPostRequest(): void
    {
        $transport = new FakeTransport(new RawResponse(200, json_encode([
            'status' => true,
            'message' => 'Transfer queued',
            'data' => ['reference' => 'TRX-456'],
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);

        $result = $paystack->transfer->initiate(['source' => 'balance', 'amount' => 5000, 'recipient' => 'RCP_123']);

        $this->assertSame('TRX-456', $result->data->reference);
        $this->assertSame('https://api.paystack.co/transfer', $transport->requests[0]['url']);
    }

    public function testAFailedApiResponseThrowsAnApiExceptionWithTheResponseObject(): void
    {
        $transport = new FakeTransport(new RawResponse(400, json_encode([
            'status' => false,
            'message' => 'Invalid amount passed',
        ])));

        $paystack = new Paystack('sk_test_abc', $transport);

        try {
            $paystack->transaction->initialize(['amount' => -1]);
            $this->fail('Expected an ApiException to be thrown.');
        } catch (ApiException $e) {
            $this->assertSame('Invalid amount passed', $e->getMessage());
            $this->assertSame(400, $e->getCode());
            $this->assertSame('Invalid amount passed', $e->getResponseObject()->message);
        }
    }

    public function testAccessingAnUnknownResourceThrows(): void
    {
        $paystack = new Paystack('sk_test_abc', new FakeTransport(new RawResponse(200, '{}')));

        $this->expectException(\InvalidArgumentException::class);

        $paystack->notaresource;
    }
}
