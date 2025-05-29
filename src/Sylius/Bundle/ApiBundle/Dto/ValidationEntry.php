<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Dto;

class ValidationEntry
{
    /** @param ValidationEntry[] $errors */
    public function __construct(
        public readonly string $propertyPath,
        public readonly string $type,
        public readonly ?string $message = null,
        public readonly array $errors = [],
    ) {
    }
}
