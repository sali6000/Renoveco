<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductAttributeSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

class ProductAttribute extends BaseModel
{
    public function __construct(
        private string $_attributeName,
        private string $_value,
        private ?string $_unit = null,
    ) {}

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
            _attributeName: self::getString($row, HelperSchemaMysql::fieldProperty(AttributeSchemaMysql::NAME)),
            _unit: self::getStringOrNull($row, HelperSchemaMysql::fieldProperty(AttributeSchemaMysql::UNIT)),
            _value: self::getString($row, HelperSchemaMysql::fieldProperty(ProductAttributeSchemaMysql::VALUE)),
        );
    }
}
