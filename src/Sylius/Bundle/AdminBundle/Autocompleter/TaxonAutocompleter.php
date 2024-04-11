<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Autocompleter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Autocomplete\OptionsAwareEntityAutocompleterInterface;

final class TaxonAutocompleter implements OptionsAwareEntityAutocompleterInterface
{
    public function __construct(private readonly string $taxonClass)
    {
    }

    public function getEntityClass(): string
    {
        return $this->taxonClass;
    }

    /** @param EntityRepository<TaxonInterface> $repository */
    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository->createQueryBuilder('o');
    }

    public function getLabel(object $entity): string
    {
        return $entity->getName();
    }

    public function getValue(object $entity): mixed
    {
        return $entity->getCode();
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }

    /** @param array<string, scalar> $options */
    public function setOptions(array $options): void
    {
    }
}
