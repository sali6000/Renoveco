<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Modules\Admin\Attribute\Domain\Entity\AttributeGroup;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeGroupQuery;
use Src\Modules\Admin\Attribute\Domain\Repository\AttributeGroupRepositoryInterface;
use Src\Modules\Attribute\Infrastructure\Schema\AttributeGroupSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\AttributeDomainSchemaMysql;

final class AttributeGroupRepositoryMysql extends RepositoryMySQL implements AttributeGroupRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PROPERTIES SCHEMES :
    //----------------------------------------------------------------------------
    private const ATTRIBUTE_GROUP_COLUMNS = [
        AttributeGroupSchemaMysql::ID,
        AttributeGroupSchemaMysql::NAME
    ];

    /** @return string Schéma table product */
    protected function getTable(): string
    {
        return AttributeGroupSchemaMysql::TABLE;
    }

    /** @return AttributeGroup Produit obtenu depuis $row */
    protected function fromArray(array $row): AttributeGroup
    {
        return AttributeGroup::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------
    public function findAttributeGroup(AttributeGroupQuery $q): ?AttributeGroup
    {
        return $this->executeFindOne($q, self::ATTRIBUTE_GROUP_COLUMNS, $this->applyFilters(...));
    }

    public function findAttributeGroups(AttributeGroupQuery $q): array
    {
        return $this->executeFindAll($q, self::ATTRIBUTE_GROUP_COLUMNS, $this->applyFilters(...));
    }

    //----------------------------------------------------------------------------
    // FILTERS MAKER :
    //----------------------------------------------------------------------------
    protected function applyFilters(QueryBuilderInterface $qb, AttributeGroupQuery $q): QueryBuilderInterface
    {
        if ($q->byName !== null) $qb = $qb->where(AttributeDomainSchemaMysql::NAME . ' = :name', [':name' => $q->byName]);
        if ($q->byId !== null) $qb = $qb->where(AttributeDomainSchemaMysql::ID . ' = :id', [':id' => $q->byId]);
        return $qb;
    }
}
