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

use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderConfirmationEmailResendCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendOrderConfirmationEmailWithValidOrderStateValidator extends ConstraintValidator
{
    /**
     * @param RepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private RepositoryInterface $orderRepository,
        private OrderConfirmationEmailResendCheckerInterface $orderConfirmationEmailResendChecker,
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
        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->orderTokenValue]);
        if (null === $order) {
            return;
        }

        if (!$this->orderConfirmationEmailResendChecker->canBeResent($order)) {
            $this->context->addViolation(
                $constraint->message,
                ['%state%' => $order->getState()],
            );
        }
    }
}
