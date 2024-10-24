<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Order\Checker\OrderConfirmationEmailResendCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderConfirmationResendCheckExtension extends AbstractExtension
{
    public function __construct(
        private OrderConfirmationEmailResendCheckerInterface $orderConfirmationEmailResendChecker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_can_order_confirmation_be_resent',
                [$this->orderConfirmationEmailResendChecker, 'canBeResent'],
            ),
        ];
    }
}
