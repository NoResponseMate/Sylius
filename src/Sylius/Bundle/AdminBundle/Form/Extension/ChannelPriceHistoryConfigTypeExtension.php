<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelPriceHistoryConfigTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxonsExcludedFromShowingLowestPrice', EntityType::class, [
                'label' => 'sylius.ui.taxons_for_which_the_lowest_price_is_not_displayed',
                'required' => false,
                'class' => Taxon::class,
                'multiple' => true,
                'expanded' => false,
                'autocomplete' => true,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ChannelPriceHistoryConfigType::class;
    }
}
