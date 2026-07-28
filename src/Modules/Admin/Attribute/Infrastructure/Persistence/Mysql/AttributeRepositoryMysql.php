<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Infrastructure\Persistence\Mysql;

use Src\Modules\Admin\Attribute\Domain\Entity\Attribute;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeGroupQuery;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeQuery;
use Src\Modules\Admin\Attribute\Domain\Repository\AttributeRepositoryInterface;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeGroupSchemaMysql;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeSchemaMysql;

final class AttributeRepositoryMysql extends RepositoryMySQL implements AttributeRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PROPERTIES SCHEMES :
    //----------------------------------------------------------------------------
    private const ATTRIBUTE_COLUMNS = [
        AttributeSchemaMysql::ID,
        AttributeSchemaMysql::NAME,
        AttributeSchemaMysql::GROUP_ID
    ];

    private const ATTRIBUTE_GROUP_COLUMNS = [
        AttributeGroupSchemaMysql::ID,
        AttributeGroupSchemaMysql::NAME
    ];

    /** @return string Schéma table product */
    protected function getTable(): string
    {
        return AttributeSchemaMysql::TABLE;
    }

    /** @return Attribute Produit obtenu depuis $row */
    protected function fromArray(array $row): Attribute
    {
        return Attribute::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------
    public function findAttribute(AttributeQuery $q): ?Attribute
    {
        return $this->executeFindOne($q, self::ATTRIBUTE_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    public function findAttributes(AttributeQuery $q): array
    {
        return $this->executeFindAll($q, self::ATTRIBUTE_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    //----------------------------------------------------------------------------
    // FILTERS MAKER :
    //----------------------------------------------------------------------------
    protected function applyFilters(QueryBuilderInterface $qb, AttributeQuery $q): QueryBuilderInterface
    {
        if ($q->byName !== null) $qb = $qb->where(AttributeSchemaMysql::NAME . ' = :name', [':name' => $q->byName]);
        if ($q->byId !== null) $qb = $qb->where(AttributeSchemaMysql::ID . ' = :id', [':id' => $q->byId]);
        return $qb;
    }


    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    protected function applyRelations(AttributeQuery $q): array
    {
        $relations = [];

        if ($q->withAttributeGroup) $relations[] = AttributeSchemaMysql::withGroup(self::ATTRIBUTE_GROUP_COLUMNS);

        return $relations;
    }
}
