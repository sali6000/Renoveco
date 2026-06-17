<?php

namespace Src\Modules\Attribute\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Domain\Infrastructure\Schema\DomainSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

// PIVOT
final class AttributeGroupDomainSchemaMysql extends HelperSchemaMysql
{
    public const TABLE = 'attribute_group_domain attdom';
    public const ID = 'attdom.id';
    public const ATTRIBUTE_GROUP_ID = 'attdom.attribute_group_id';
    public const DOMAIN_ID = 'attdom.domain_id';

    // RELATION PIVOT
    public static function withDomains(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(

            // TO
            key: self::fieldTable(DomainSchemaMysql::TABLE), // KEY FOR ARRAY (ex: User['roles'][...])
            relationColumns: $columns, // COLUMNS TO RETURN (ex: [self::NAME, self::DESCRIPTION, ... ])
            relatedTable: DomainSchemaMysql::TABLE,
            foreignKey: DomainSchemaMysql::ID,

            // FROM
            localKey: AttributeGroupSchemaMysql::ID,

            // PIVOT
            pivotTable: self::TABLE, // BY : PIVOT TABLE
            pivotForeignKey: self::DOMAIN_ID, // TO : ID
            pivotLocalKey: self::ATTRIBUTE_GROUP_ID, // FROM : ID
        );
    }
}
