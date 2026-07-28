<?php

namespace Src\Modules\Admin\Attribute\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Admin\Attribute\Domain\Entity\AttributeGroup;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeGroupSchemaMysql;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

class Attribute extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string    $_name,
        private AttributeGroup      $_attributeGroup,

        // Optionnels
        private ?int      $_id = null,
        private ?string   $_type = null,
        private ?string   $_unit = null,
        private ?bool     $_isRequired = null,
        private ?int      $_parentAttributeId = null,
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public AttributeGroup $attributeGroup {
        get => $this->_attributeGroup;
        set(AttributeGroup $value) => $this->_attributeGroup = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $type {
        get => $this->_type;
        set(?string $value) => $this->_type = $value;
    }

    public ?string $unit {
        get => $this->_unit;
        set(?string $value) => $this->_unit = $value;
    }

    public ?bool $isRequired {
        get => $this->_isRequired;
        set(?bool $value) => $this->_isRequired = $value;
    }

    public ?int $parentAttributeId {
        get => $this->_parentAttributeId;
        set(?int $value) => $this->_parentAttributeId = $value;
    }


    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _name: self::getString($row, AttributeSchemaMysql::NAME),
            _attributeGroup: self::getMapped($row, HelperSchemaMysql::fieldTable(AttributeGroupSchemaMysql::TABLE), [AttributeGroup::class, 'fromArray']),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, AttributeSchemaMysql::ID),
            _type: self::getStringOrNull($row, AttributeSchemaMysql::TYPE),
            _unit: self::getStringOrNull($row, AttributeSchemaMysql::UNIT),
            _isRequired: self::getBoolOrFalse($row, AttributeSchemaMysql::IS_REQUIRED),
            _parentAttributeId: self::getIntOrNull($row, AttributeSchemaMysql::PARENT_ID)
        );
    }
}
