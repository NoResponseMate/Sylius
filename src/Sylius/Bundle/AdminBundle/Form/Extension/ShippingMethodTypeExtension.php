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

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\AddButtonType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    /** @param array<string, string> $types */
    public function __construct(private readonly array $types)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rules', LiveCollectionType::class, [
                'entry_type' => ShippingMethodRuleType::class,
                'entry_options' => [
                    'types' => $this->types,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_type' => AddButtonType::class,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_rule',
                    'types' => $this->types,
                ],
                'button_delete_options' => [
                    'label' => false,
                ],
            ])
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        yield ShippingMethodType::class;
    }
}