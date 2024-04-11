<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class ChannelPriceHistoryConfigTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly DataTransformerInterface $taxonsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxonsExcludedFromShowingLowestPrice', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.taxons_for_which_the_lowest_price_is_not_displayed',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ])
        ;

        $builder->get('taxonsExcludedFromShowingLowestPrice')
            ->addModelTransformer(
                new ReversedTransformer($this->taxonsToCodesTransformer)
            )
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ChannelPriceHistoryConfigType::class;
    }
}
