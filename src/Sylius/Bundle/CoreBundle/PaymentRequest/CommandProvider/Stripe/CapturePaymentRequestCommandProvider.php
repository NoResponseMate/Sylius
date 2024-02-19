<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider\Stripe;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Stripe\CapturePaymentRequest;
use Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

class CapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return
            $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_CAPTURE &&
            $paymentRequest->getPayment()->getState() === PaymentInterface::STATE_AUTHORIZED
        ;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new CapturePaymentRequest($paymentRequest->getHash());
    }
}
