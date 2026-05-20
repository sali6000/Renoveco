<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;

class ProductAttribute extends BaseModel
{
    public function __construct(
        private string $_groupName,
        private string $_attributeName,
        private string $_value,
        private ?string $_unit = null,
    ) {}

    public string $groupName {
        get => $this->_groupName;
        set(string $value) => $this->_groupName = $value;
    }

    public string $attributeName {
        get => $this->_attributeName;
        set(string $value) => $this->_attributeName = $value;
    }

    public string $value {
        get => $this->_value;
        set(string $value) => $this->_value = $value;
    }

    public ?string $unit {
        get => $this->_unit;
        set(?string $value) => $this->_unit = $value;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            _groupName: $row['group_name'],
            _attributeName: $row['attribute_name'],
            _value: self::getString($row, SchemaMysql::PRODUCT_ATTRIBUTE_VALUE),
            _unit: self::getStringOrNull($row, SchemaMysql::ATTRIBUTES_UNIT),
        );
    }
}
