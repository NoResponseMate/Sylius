<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Order\Checker;

use Sylius\Component\Order\Model\OrderInterface;

final readonly class OrderConfirmationEmailResendChecker implements OrderConfirmationEmailResendCheckerInterface
{
    /** @param string[] $orderStates */
    public function __construct(private array $orderStates)
    {
    }

    public function canBeResent(OrderInterface $order): bool
    {
        return in_array($order->getState(), $this->orderStates, true);
    }
}
