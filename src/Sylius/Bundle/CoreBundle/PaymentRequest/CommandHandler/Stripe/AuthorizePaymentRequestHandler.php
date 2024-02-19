<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Stripe;

use Stripe\Card;
use Stripe\Charge;
use Stripe\StripeClientInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Stripe\AuthorizePaymentRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AuthorizePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private StripeClientInterface $client,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function __invoke(AuthorizePaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestRepository->findOneByHash($capturePaymentRequest->getHash());
        Assert::notNull($paymentRequest);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $this->createCharge($paymentRequest);
    }

    private function createCharge(PaymentRequestInterface $paymentRequest): void
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);
        $order = $payment->getOrder();

        $paymentRequest->setRequestPayload([
            'capture' => false,
            'amount' => $payment->getAmount(),
            'currency' => $payment->getCurrencyCode(),
            'description' => $order->getTokenValue() . ' - ' . $order->getTotal() . ' mula',
        ]);

        $customer = $this->client->customers->retrieve('cus_PZc1ixdIjd8uLu');

        /** @var Charge $charge */
        $charge = $this->client->charges->create(array_merge($paymentRequest->getRequestPayload(), [
            'customer' => $customer->id,
        ]));

        $paymentRequest->setResponseData($charge->toArray());
        $payment->setDetails($charge->toArray());
        if ($charge->status === Charge::STATUS_FAILED) {
            $paymentRequest->setState(PaymentRequestInterface::STATE_FAILED);
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS);
        }
        if ($charge->status === Charge::STATUS_SUCCEEDED) {
            $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED);
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_AUTHORIZE);
        }
    }
}
