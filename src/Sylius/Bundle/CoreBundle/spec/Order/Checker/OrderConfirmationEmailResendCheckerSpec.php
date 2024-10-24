<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Order\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderConfirmationEmailResendCheckerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'new',
            'fulfilled',
        ]);
    }

    function it_returns_true_if_order_is_in_valid_state(OrderInterface $order): void
    {
        $order->getState()->willReturn('new');

        $this->canBeResent($order)->shouldReturn(true);
    }

    function it_returns_false_if_order_is_not_in_valid_state(OrderInterface $order): void
    {
        $order->getState()->willReturn('cart');

        $this->canBeResent($order)->shouldReturn(false);
    }
}
