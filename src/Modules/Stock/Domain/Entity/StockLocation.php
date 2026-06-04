<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;

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
            _name: self::getString($row, SchemaMysql::STOCK_LOCATION_NAME),
            _id: self::getIntOrNull($row, SchemaMysql::STOCK_LOCATION_ID),
            _description: self::getStringOrNull($row, SchemaMysql::STOCK_LOCATION_DESCRIPTION),
        );
    }
}
