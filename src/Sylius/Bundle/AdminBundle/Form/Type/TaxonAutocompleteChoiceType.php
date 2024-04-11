<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(route: 'sylius_admin_entity_autocomplete_admin')]
final class TaxonAutocompleteChoiceType extends AbstractType
{
    public function __construct (
        private readonly string $taxonClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->taxonClass,
            'choice_name' => 'name',
            'choice_value' => 'code',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_taxon_autocomplete_choice';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
