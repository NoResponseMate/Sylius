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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $persister): void
    {
        $this->beConstructedWith($persister);
    }

    function it_is_a_context_aware_data_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_catalog_promotion_interface(
        CatalogPromotionInterface $data,
    ): void {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports(new \ArrayObject())->shouldReturn(false);

        $this->supports($data)->shouldReturn(true);
    }

    function it_delegates_removal_to_decorated_persister(
        ContextAwareDataPersisterInterface $persister,
        CatalogPromotionInterface $data,
    ): void {
        $persister->remove($data, [])->shouldBeCalled();

        $this->remove($data);
    }

    function it_delegates_persisting_to_decorated_persister_when_no_scopes_are_present(
        ContextAwareDataPersisterInterface $persister,
        CatalogPromotionInterface $data,
    ): void {
        $data->getScopes()->willReturn(new ArrayCollection());
        $persister->persist($data, [])->shouldBeCalled();

        $this->persist($data);
    }

    function it_deduplicates_scopes_configuration(
        ContextAwareDataPersisterInterface $persister,
        CatalogPromotionInterface $data,
        CatalogPromotionScopeInterface $taxonScope,
        CatalogPromotionScopeInterface $variantScope,
        CatalogPromotionScopeInterface $productScope,
    ): void {
        $data->getScopes()->willReturn(new ArrayCollection([
            $taxonScope->getWrappedObject(),
            $variantScope->getWrappedObject(),
            $productScope->getWrappedObject(),
        ]));

        $taxonScope->getConfiguration()->willReturn([
            'taxons' => [
                'taxon1',
                'taxon2',
                'taxon1',
            ],
        ]);
        $taxonScope->setConfiguration([
            'taxons' => [
                'taxon1',
                'taxon2',
            ],
        ])->shouldBeCalled();

        $variantScope->getConfiguration()->willReturn([
            'variants' => [
                'variant1',
                'variant2',
                'variant1',
            ],
        ]);
        $variantScope->setConfiguration([
            'variants' => [
                'variant1',
                'variant2',
            ],
        ])->shouldBeCalled();

        $productScope->getConfiguration()->willReturn([
            'products' => [
                'product1',
                'product2',
                'product1',
            ],
        ]);
        $productScope->setConfiguration([
            'products' => [
                'product1',
                'product2',
            ],
        ])->shouldBeCalled();

        $persister->persist($data, [])->shouldBeCalled();

        $this->persist($data);
    }
}
