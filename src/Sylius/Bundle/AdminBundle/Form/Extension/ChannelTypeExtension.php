<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(private readonly TaxonRepositoryInterface $taxonRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('menuTaxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.channel.menu_taxon',
                'multiple' => false,
                'choice_value' => 'code',
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ChannelType::class;
    }
}
