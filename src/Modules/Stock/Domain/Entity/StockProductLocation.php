<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;
use DateTime;
use Src\Database\SchemaMysql;

class StockProductLocation extends BaseModel
{
    public function __construct(

        // Obligatoire
        private int $_productStockId,
        private int $_stockLocationId,
        private int $_quantity,

        // Optionnel
        private ?int $_id = null,
        private ?DateTime $_createdAt = null,
        private ?DateTime $_updatedAt = null
    ) {}

    public int $productStockId {
        get => $this->_productStockId;
        set(int $value) => $this->_productStockId = $value;
    }

    public int $stockLocationId {
        get => $this->_stockLocationId;
        set(int $value) => $this->_stockLocationId = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $value) => $this->_createdAt = $value;
    }

    public ?DateTime $updatedAt {
        get => $this->_updatedAt;
        set(?DateTime $value) => $this->_updatedAt = $value;
    }

    // ==========================================================
    // FACTORY — hydratation depuis un row DB
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(
            _productStockId: self::getInt($row, SchemaMysql::STOCK_PRODUCT_LOCATION_PRODUCT_STOCK_ID),
            _stockLocationId: self::getInt($row, SchemaMysql::STOCK_PRODUCT_LOCATION_STOCK_LOCATION_ID),
            _quantity: self::getInt($row, SchemaMysql::STOCK_PRODUCT_LOCATION_QUANTITY),
            _id: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_LOCATION_ID),
            _createdAt: self::getDateOrNull($row, SchemaMysql::STOCK_PRODUCT_LOCATION_CREATED_AT),
            _updatedAt: self::getDateOrNull($row, SchemaMysql::STOCK_PRODUCT_LOCATION_UPDATED_AT),
        );
    }
}
