<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Dto;

class ResourceValidation
{
    /** @param ValidationEntry[] $errors */
    public function __construct(
        public readonly string $resourceClass,
        public readonly array $errors,
    ) {
    }
}
