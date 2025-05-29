<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Common;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Converter\ValidatorMetadataConverterInterface;
use Sylius\Bundle\ApiBundle\Dto\ResourceValidation;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ResourceValidation> */
final readonly class ValidationProvider implements ProviderInterface
{
    public function __construct(
        private ValidatorMetadataConverterInterface $metadataConverter,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        Assert::true(is_a($operation->getClass(), ResourceValidation::class, true));
        Assert::isInstanceOf($operation, Get::class);
        Assert::keyExists($uriVariables, 'resourceClass');
        Assert::string($uriVariables['resourceClass']);
        Assert::notEmpty($uriVariables['resourceClass']);

        $resourceClass= $uriVariables['resourceClass'];

//        if (!$this->metadataFactory->hasMetadataFor($resourceClass)) {
//            return null;
//        }

        $result = $this->metadataConverter->convert($resourceClass);

        return $result;
    }
}
