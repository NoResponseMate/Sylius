<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Converter;

use Sylius\Bundle\ApiBundle\Dto\ResourceValidation;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

interface ValidatorMetadataConverterInterface
{
    public function convert(string $className): array;
}
