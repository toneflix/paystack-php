<?php

namespace Toneflix\Paystack\Resources;

use Toneflix\Paystack\Paystack;

abstract class AbstractResource
{
    public function __construct(protected readonly Paystack $client)
    {
    }
}
