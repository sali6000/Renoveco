<?php

namespace Src\Modules\Admin\Attribute\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Attribute\Infrastructure\Schema\AttributeGroupSchemaMysql;
use Src\Modules\Domain\Infrastructure\Schema\DomainSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

class AttributeDomain extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string    $_name,

        // Optionnels
        private ?int      $_id = null,
        private ?string   $_description = null,

        /** @var AttributeGroup[] */
        private array    $_attributeGroups        = [],
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $description {
        get => $this->_description;
        set(?string $value) => $this->_description = $value;
    }

    /** @return AttributeGroup[] */
    public array $attributeGroups {
        get => $this->_attributeGroups;
        set(array $value) => $this->_attributeGroups = $value;
    }

    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _name: self::getString($row, DomainSchemaMysql::NAME),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, DomainSchemaMysql::ID),
            _description: self::getStringOrNull($row, DomainSchemaMysql::DESCRIPTION),
            _attributeGroups: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(AttributeGroupSchemaMysql::TABLE), [AttributeGroupSchemaMysql::class, 'fromArray'])
        );
    }
}
