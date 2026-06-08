<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;

class ProductStock extends BaseModel
{
    public function __construct(
        public readonly int $quantity,
        public readonly int $stockMinimum,
        public readonly int $stockMaximum,
    ) {}

    public function isLow(): bool
    {
        return $this->quantity <= $this->stockMinimum;
    }

    public function isOut(): bool
    {
        return $this->quantity === 0;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            quantity: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_QUANTITY) ?? 0,
            stockMinimum: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_STOCK_MINIMUM) ?? 0,
            stockMaximum: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_STOCK_MAXIMUM) ?? 0,
        );
    }
}
