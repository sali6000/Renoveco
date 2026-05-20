<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use DateTime;
use Src\Database\SchemaMysql;

class ProductInventory extends BaseModel
{
    public function __construct(
        private int $_productId,
        private ?int $_id = null,
        private ?int $_stockQuantity = null,
        private ?int $_stockMinimum = null,
        private ?int $_stockMaximum = null,
        private ?DateTime $_lastStockUpdate = null
    ) {}

    // ==========================================================
    // = GETTERS / SETTERS (Hook)
    // ==========================================================
    public int $productId {
        get => $this->_productId;
        set(int $value) => $this->_productId = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?int $stockQuantity {
        get => $this->_stockQuantity;
        set(?int $value) => $this->_stockQuantity = $value;
    }

    public ?int $stockMinimum {
        get => $this->_stockMinimum;
        set(?int $value) => $this->_stockMinimum = $value;
    }

    public ?int $stockMaximum {
        get => $this->_stockMaximum;
        set(?int $value) => $this->_stockMaximum = $value;
    }

    public ?DateTime $lastStockUpdate {
        get => $this->_lastStockUpdate;
        set(?DateTime $value) => $this->_lastStockUpdate = $value;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            _productId: self::getInt($row, SchemaMysql::PRODUCT_INVENTORY_PRODUCT_ID),
            _id: self::getIntOrNull($row, SchemaMysql::PRODUCT_INVENTORY_ID),
            _stockQuantity: self::getIntOrNull($row, SchemaMysql::PRODUCT_INVENTORY_STOCK_QUANTITY),
            _stockMinimum: self::getIntOrNull($row, SchemaMysql::PRODUCT_INVENTORY_STOCK_MINIMUM),
            _stockMaximum: self::getIntOrNull($row, SchemaMysql::PRODUCT_INVENTORY_STOCK_MAXIMUM),
            _lastStockUpdate: self::getDateOrNull($row, SchemaMysql::PRODUCT_INVENTORY_LAST_STOCK_UPDATE),
        );
    }
}
