<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Stripe;

use Stripe\Charge;
use Stripe\StripeClientInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Stripe\CapturePaymentRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class CapturePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private StripeClientInterface $client,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestRepository->findOneByHash($capturePaymentRequest->getHash());
        Assert::notNull($paymentRequest);

        if ([] === $paymentRequest->getResponseData()) {
            $this->retrieveCharge($paymentRequest);

            return;
        }

        $this->capture($paymentRequest);
    }

    private function retrieveCharge(PaymentRequestInterface $paymentRequest): void
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        /** @var array<string, string> $paymentDetails */
        $paymentDetails = $payment->getDetails();

        /** @var Charge $charge */
        $charge = $this->client->charges->retrieve($paymentDetails['id']);
        $paymentRequest->setResponseData($charge->toArray());

        $paymentRequest->setState(PaymentRequestInterface::STATE_PROCESSING);
    }

    private function capture(PaymentRequestInterface $paymentRequest): void
    {
        $chargeData = $paymentRequest->getResponseData();

        $charge = Charge::constructFrom($chargeData);
        $charge = $this->client->charges->capture($charge->id);

        $paymentRequest->setResponseData($charge->toArray());

        $payment = $paymentRequest->getPayment();
        $payment->setDetails($charge->toArray());

        if ($charge->status === Charge::STATUS_FAILED) {
            $paymentRequest->setState(PaymentRequestInterface::STATE_FAILED);
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_FAIL);
        }
        if ($charge->status === Charge::STATUS_SUCCEEDED) {
            $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED);
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE);
        }
    }
}
