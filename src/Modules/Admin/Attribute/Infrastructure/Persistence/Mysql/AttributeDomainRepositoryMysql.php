<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Modules\Admin\Attribute\Domain\Entity\AttributeDomain;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeDomainQuery;
use Src\Modules\Admin\Attribute\Domain\Repository\AttributeDomainRepositoryInterface;
use Src\Modules\Product\Infrastructure\Schema\AttributeDomainSchemaMysql;

final class AttributeDomainRepositoryMysql extends RepositoryMySQL implements AttributeDomainRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PROPERTIES SCHEMES :
    //----------------------------------------------------------------------------
    private const ATTRIBUTE_DOMAIN_COLUMNS = [
        AttributeDomainSchemaMysql::ID,
        AttributeDomainSchemaMysql::NAME,
        AttributeDomainSchemaMysql::DESCRIPTION
    ];

    /** @return string Schéma table product */
    protected function getTable(): string
    {
        return AttributeDomainSchemaMysql::TABLE;
    }

    /** @return AttributeDomain Produit obtenu depuis $row */
    protected function fromArray(array $row): AttributeDomain
    {
        return AttributeDomain::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------
    public function findAttribute(AttributeDomainQuery $q): ?AttributeDomain
    {
        return $this->executeFindOne($q, self::ATTRIBUTE_DOMAIN_COLUMNS, $this->applyFilters(...));
    }

    public function findAttributes(AttributeDomainQuery $q): array
    {
        return $this->executeFindAll($q, self::ATTRIBUTE_DOMAIN_COLUMNS, $this->applyFilters(...));
    }

    //----------------------------------------------------------------------------
    // FILTERS MAKER :
    //----------------------------------------------------------------------------
    protected function applyFilters(QueryBuilderInterface $qb, AttributeDomainQuery $q): QueryBuilderInterface
    {
        if ($q->byName !== null) $qb = $qb->where(AttributeDomainSchemaMysql::NAME . ' = :name', [':name' => $q->byName]);
        if ($q->byId !== null) $qb = $qb->where(AttributeDomainSchemaMysql::ID . ' = :id', [':id' => $q->byId]);
        return $qb;
    }
}
