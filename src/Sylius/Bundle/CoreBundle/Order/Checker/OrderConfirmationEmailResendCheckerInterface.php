<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Order\Checker;

use Sylius\Component\Order\Model\OrderInterface;

interface OrderConfirmationEmailResendCheckerInterface
{
    public function canBeResent(OrderInterface $order): bool;
}
