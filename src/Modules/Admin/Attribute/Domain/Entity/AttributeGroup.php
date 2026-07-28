<?php

namespace Src\Modules\Admin\Attribute\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeGroupSchemaMysql;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

class AttributeGroup extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string    $_name,

        // Optionnels
        private ?int      $_id = null,
        private ?int   $_displayOrder = null,

        /** @var Attribute[] */
        private array    $_attributes        = [],
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?int $displayOrder {
        get => $this->_displayOrder;
        set(?int $value) => $this->_displayOrder = $value;
    }

    /** @return Attribute[] */
    public array $attributes {
        get => $this->_attributes;
        set(array $value) => $this->_attributes = $value;
    }

    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _name: self::getString($row, AttributeGroupSchemaMysql::NAME),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, AttributeGroupSchemaMysql::ID),
            _displayOrder: self::getIntOrNull($row, AttributeGroupSchemaMysql::DISPLAY_ORDER),
            _attributes: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(AttributeSchemaMysql::TABLE), [Attribute::class, 'fromArray'])
        );
    }
}
