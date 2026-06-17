<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;
use Src\Modules\Stock\Domain\Infrastructure\Schema\StockLocationSchemaMysql;

class StockLocation extends BaseModel
{
    public function __construct(

        // Obligatoire
        private string $_name,

        // Optionnelle
        private ?int $_id = null,
        private ?string $_description = null,
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


    // ==========================================================
    // FACTORY — hydratation depuis un row DB
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(
            _name: self::getString($row, HelperSchemaMysql::fieldProperty(StockLocationSchemaMysql::NAME)),
            _id: self::getIntOrNull($row, HelperSchemaMysql::fieldProperty(StockLocationSchemaMysql::ID)),
            _description: self::getStringOrNull($row, HelperSchemaMysql::fieldProperty(StockLocationSchemaMysql::DESCRIPTION)),
        );
    }
}
