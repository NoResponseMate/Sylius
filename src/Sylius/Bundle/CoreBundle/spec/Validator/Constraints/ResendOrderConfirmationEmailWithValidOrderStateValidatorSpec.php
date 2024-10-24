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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Message\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderConfirmationEmailResendCheckerInterface;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderState;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendOrderConfirmationEmailWithValidOrderStateValidatorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $orderRepository,
        OrderConfirmationEmailResendCheckerInterface $orderConfirmationEmailResendChecker,
    ): void {
        $this->beConstructedWith($orderRepository, $orderConfirmationEmailResendChecker);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_resend_order_confirmation_email_with_valid_order_state(
        ExecutionContextInterface $context,
        Constraint $constraint,
    ): void {
        $this->initialize($context);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [new ResendOrderConfirmationEmail('TOKEN'), $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_a_resend_order_confirmation_email(
        ExecutionContextInterface $context,
    ): void {
        $this->initialize($context);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [new \stdClass(), new ResendOrderConfirmationEmailWithValidOrderState()])
        ;
    }

    function it_does_nothing_when_order_does_not_exist(
        RepositoryInterface $orderRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->initialize($context);

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn(null);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(new ResendOrderConfirmationEmail('TOKEN'), new ResendOrderConfirmationEmailWithValidOrderState());
    }

    function it_does_nothing_if_the_state_is_valid(
        RepositoryInterface $orderRepository,
        OrderConfirmationEmailResendCheckerInterface $orderConfirmationEmailResendChecker,
        ExecutionContextInterface $context,
        OrderInterface $order,
    ): void {
        $this->initialize($context);

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);

        $orderConfirmationEmailResendChecker->canBeResent($order)->willReturn(true);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ResendOrderConfirmationEmail('TOKEN'),
            new ResendOrderConfirmationEmailWithValidOrderState(),
        );
    }

    function it_does_nothing_if_the_state_is_valid_using_states_array(
        RepositoryInterface $orderRepository,
        ExecutionContextInterface $context,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($orderRepository, [OrderInterface::STATE_NEW]);
        $this->initialize($context);

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ResendOrderConfirmationEmail('TOKEN'),
            new ResendOrderConfirmationEmailWithValidOrderState(),
        );
    }

    function it_adds_a_violation_if_order_has_invalid_state(
        RepositoryInterface $orderRepository,
        OrderConfirmationEmailResendCheckerInterface $orderConfirmationEmailResendChecker,
        OrderInterface $order,
        ExecutionContextInterface $context,
    ): void {
        $this->initialize($context);

        $constraint = new ResendOrderConfirmationEmailWithValidOrderState();

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);

        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $orderConfirmationEmailResendChecker->canBeResent($order)->willReturn(false);

        $context
            ->addViolation($constraint->message, ['%state%' => OrderInterface::STATE_FULFILLED])
            ->shouldBeCalled()
        ;

        $this->validate(
            new ResendOrderConfirmationEmail('TOKEN'),
            $constraint,
        );
    }

    function it_adds_a_violation_if_order_has_invalid_state_using_states_array(
        RepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $context,
    ): void {
        $this->beConstructedWith($orderRepository, [OrderInterface::STATE_NEW]);
        $this->initialize($context);

        $constraint = new ResendOrderConfirmationEmailWithValidOrderState();

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $context
            ->addViolation($constraint->message, ['%state%' => OrderInterface::STATE_FULFILLED])
            ->shouldBeCalled()
        ;

        $this->validate(
            new ResendOrderConfirmationEmail('TOKEN'),
            $constraint,
        );
    }
}
