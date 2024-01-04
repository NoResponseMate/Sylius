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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof CatalogPromotionInterface;
    }

    /**
     * @param CatalogPromotionInterface $data
     * @param array<string, mixed> $context
     */
    public function persist($data, array $context = [])
    {
        foreach ($data->getScopes() as $scope) {
            $configuration = $this->deduplicateConfiguration($scope->getConfiguration());
            $scope->setConfiguration($configuration);
        }

        return $this->decoratedDataPersister->persist($data, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<string, mixed>
     */
    private function deduplicateConfiguration(array $configuration): array
    {
        foreach ($configuration as &$item) {
            if (is_array($item)) {
                $item = $this->deduplicateConfiguration($item);
            }
        }
        unset($item);

        return array_unique($configuration, SORT_REGULAR);
    }
}
