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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_product_interface_and_shop_api_section(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        ProductInterface $product,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this->supportsNormalization($order, null, ['groups' => ['sylius:product:index']])->shouldReturn(false);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this->supportsNormalization($product, null, ['groups' => ['sylius:product:index']])->shouldReturn(true);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this->supportsNormalization($product, null, ['groups' => ['sylius:product:show']])->shouldReturn(false);

        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this->supportsNormalization($product, null, ['groups' => ['sylius:product:index']])->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        ProductInterface $product,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this
            ->supportsNormalization($product, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->shouldReturn(false)
        ;
    }

    function it_adds_default_variant_iri_to_serialized_product(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this->setNormalizer($normalizer);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([]);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));
        $defaultProductVariantResolver->getVariant($product)->willReturn($variant);
        $iriConverter->getIriFromResource($variant)->willReturn('/api/v2/shop/product-variants/CODE');

        $this->normalize($product, null, ['groups' => ['sylius:product:index']])->shouldReturn([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => '/api/v2/shop/product-variants/CODE',
            'defaultVariantData' => null,
        ]);
    }

    function it_adds_default_variant_iri_to_serialized_product_when_object_normalizer_is_set_and_variant_has_no_data_serialized(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        AbstractObjectNormalizer $objectNormalizer,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith(
            $defaultProductVariantResolver,
            $iriConverter,
            $sectionProvider,
            ['sylius:product:index'],
            $objectNormalizer,
        );

        $this->setNormalizer($normalizer);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([]);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));
        $defaultProductVariantResolver->getVariant($product)->willReturn($variant);

        $objectNormalizer->normalize($variant, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([]);

        $iriConverter
            ->getIriFromResource($variant)
            ->willReturn('/api/v2/shop/product-variants/CODE')
        ;

        $this->normalize($product, null, ['groups' => ['sylius:product:index']])->shouldReturn([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => '/api/v2/shop/product-variants/CODE',
            'defaultVariantData' => null,
        ]);
    }

    function it_adds_serialized_default_variant_to_serialized_product_when_object_normalizer_is_set_and_variant_has_data_serialized(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        AbstractObjectNormalizer $objectNormalizer,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith(
            $defaultProductVariantResolver,
            $iriConverter,
            $sectionProvider,
            ['sylius:product:index'],
            $objectNormalizer,
        );

        $this->setNormalizer($normalizer);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([]);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));
        $defaultProductVariantResolver->getVariant($product)->willReturn($variant);

        $objectNormalizer->normalize($variant, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([
            'code' => 'CODE',
        ]);
        $normalizer->normalize($variant, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([
            'code' => 'CODE',
        ]);

        $iriConverter
            ->getIriFromResource($variant)
            ->willReturn('/api/v2/shop/product-variants/CODE')
        ;

        $this->normalize($product, null, ['groups' => ['sylius:product:index']])->shouldIterateLike([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => '/api/v2/shop/product-variants/CODE',
            'defaultVariantData' => ['code' => 'CODE'],
        ]);
    }

    function it_adds_default_variant_field_with_null_value_to_serialized_product_if_there_is_no_default_variant(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
        ProductInterface $product,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this->setNormalizer($normalizer);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->willReturn([]);
        $iriConverter->getIriFromResource($variant)->willReturn('/api/v2/shop/product-variants/CODE');
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));

        $defaultProductVariantResolver->getVariant($product)->willReturn(null);

        $this->normalize($product, null, ['groups' => ['sylius:product:index']])->shouldReturn([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => null,
            'defaultVariantData' => null,
        ]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ProductInterface $product,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this->setNormalizer($normalizer);

        $normalizer->normalize($product, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$product, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ]])
        ;
    }

    public function it_throws_an_exception_if_serialization_group_is_not_supported(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ShopApiSection $shopApiSection,
        ProductInterface $product,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider, ['sylius:product:index']);

        $this->setNormalizer($normalizer);

        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($product, null, [
            'groups' => ['sylius:product:show'],
        ])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$product, null, [
                'groups' => ['sylius:product:show'],
            ]])
        ;
    }
}
