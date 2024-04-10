<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('menuTaxon', EntityType::class, [
                'label' => 'sylius.form.channel.menu_taxon',
                'class' => Taxon::class,
                'multiple' => false,
                'autocomplete' => true,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ChannelType::class;
    }
}
