<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Converter;

use Sylius\Bundle\ApiBundle\Dto\ResourceValidation;
use Sylius\Bundle\ApiBundle\Dto\ValidationEntry;
use Symfony\Component\Validator\Mapping\CascadingStrategy;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use Webmozart\Assert\Assert;

final class ValidatorMetadataConverter implements ValidatorMetadataConverterInterface
{
    /** @param array<array-key, class-string> $allowedConstraints */
    public function __construct(
        private MetadataFactoryInterface $metadataFactory,
        private array $allowedConstraints = [],
    ) {
    }

    public function convert(string $className, array &$visited = [], string $prefix = ''): array
    {
        if (false === $this->metadataFactory->hasMetadataFor($className)) {
            return [];
        }

        return $this->getConstraints($className);
    }

    private function getConstraints(string $className, array &$visited = [], string $prefix = ''): array
    {

        if (in_array($className, $visited, true)) {
            return [];
        }

        $visited[] = $className;

        /** @var ClassMetadataInterface $metadata */
        $metadata = $this->metadataFactory->getMetadataFor($className);

        $flatConstraints = [];

        // Class-level constraints (on the class itself, not properties)
        foreach ($metadata->getConstraints() as $constraint) {
            $key = rtrim($prefix, '.') ?: $className;
            $flatConstraints[] = [
                'path' => $key,
                'constraint' => get_class($constraint),
                'message' => $constraint->message ?? null,
            ];
        }

        // Property-level constraints
        foreach ($metadata->getConstrainedProperties() as $propertyName) {
            $propertyMetadatas = $metadata->getPropertyMetadata($propertyName);

            foreach ($propertyMetadatas as $propertyMetadata) {
                if (!$propertyMetadata instanceof PropertyMetadataInterface) {
                    continue;
                }

                $propertyPath = $prefix . $propertyName;
                $hasValid = false;

                foreach ($propertyMetadata->getConstraints() as $constraint) {
                    $constraintClass = get_class($constraint);
                    $flatConstraints[] = [
                        'path' => $propertyPath,
                        'constraint' => $constraintClass,
                        'message' => $constraint->message ?? null,
                    ];
                }

                // Check if @Valid (cascading validation) is enabled
                if ($propertyMetadata->getCascadingStrategy() !== CascadingStrategy::NONE) {
                    $flatConstraints[] = [
                        'path' => $propertyPath,
                        'constraint' => 'Symfony\Component\Validator\Constraints\Valid',
                        'message' => null,
                    ];
                    $hasValid = true;
                }
                if (false === $hasValid) {
                    continue;
                }

                // Recurse into nested class if @Valid is present
                try {
                    $reflectionProperty = new \ReflectionProperty($className, $propertyName);
                    $type = $reflectionProperty->getType();

                    if ($type && !$type->isBuiltin()) {
                        $nestedClass = $type->getName();

                        if (class_exists($nestedClass)) {
                            $nestedConstraints = $this->getConstraints(
                                $nestedClass,
                                $visited,
                                $propertyPath . '.'
                            );
                            $flatConstraints = array_merge($flatConstraints, $nestedConstraints);
                        }
                    }
                } catch (\ReflectionException) {
                    // Ignore properties that can't be reflected
                }
            }
        }

        return $flatConstraints;
    }

    private function filterConstraints(array $constraints): array
    {
        return array_filter(
            $constraints,
            static fn (string $constraint): bool => in_array($constraint, $this->allowedConstraints, true),
        );
    }
}
