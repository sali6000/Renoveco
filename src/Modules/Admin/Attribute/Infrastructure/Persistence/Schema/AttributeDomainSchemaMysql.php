<?php

namespace Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema;

use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class AttributeDomainSchemaMysql extends HelperSchemaMysql
{
    public const TABLE = 'domain dom';
    public const ID = 'dom.id';
    public const NAME = 'dom.name';
    public const DESCRIPTION = 'dom.description';
}
