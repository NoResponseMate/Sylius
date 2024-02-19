<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Command\Stripe;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;

class AuthorizePaymentRequest implements PaymentRequestHashAwareInterface
{
    public function __construct(
        protected string $hash,
    ) {
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}
