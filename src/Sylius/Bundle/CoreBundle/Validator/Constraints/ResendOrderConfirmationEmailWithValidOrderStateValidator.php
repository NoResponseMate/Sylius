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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\CoreBundle\Message\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderConfirmationEmailResendCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendOrderConfirmationEmailWithValidOrderStateValidator extends ConstraintValidator
{
    /**
     * @param RepositoryInterface<OrderInterface> $orderRepository
     * @param array<string, string>|OrderConfirmationEmailResendCheckerInterface $orderStatesToAllowResendingConfirmationEmail
     */
    public function __construct(
        private RepositoryInterface $orderRepository,
        private array|OrderConfirmationEmailResendCheckerInterface $orderStatesToAllowResendingConfirmationEmail,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ResendOrderConfirmationEmail) {
            throw new UnexpectedTypeException($value, ResendOrderConfirmationEmail::class);
        }

        if (!$constraint instanceof ResendOrderConfirmationEmailWithValidOrderState) {
            throw new UnexpectedTypeException($constraint, ResendOrderConfirmationEmailWithValidOrderState::class);
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);
        if (null === $order) {
            return;
        }

        if (is_array($this->orderStatesToAllowResendingConfirmationEmail)) {
            if (!in_array($order->getState(), $this->orderStatesToAllowResendingConfirmationEmail, true)) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%state%' => $order->getState()],
                );
            }

            return;
        }

        if (!$this->orderStatesToAllowResendingConfirmationEmail->canBeResent($order)) {
            $this->context->addViolation(
                $constraint->message,
                ['%state%' => $order->getState()],
            );
        }
    }
}
