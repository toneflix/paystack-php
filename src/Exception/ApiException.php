<?php

namespace Toneflix\Paystack\Exception;

class ApiException extends \RuntimeException
{
    private ?object $responseObject = null;

    public function __construct(string $message, int $code = 0, ?object $responseObject = null)
    {
        parent::__construct($message, $code);

        $this->responseObject = $responseObject;
    }

    public function getResponseObject(): ?object
    {
        return $this->responseObject;
    }

    public function withResponseObject(?object $responseObject): static
    {
        $this->responseObject = $responseObject;

        return $this;
    }
}
