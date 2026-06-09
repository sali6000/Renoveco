<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Product\Infrastructure\Schema\ProductStockSchemaMysql;

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
            quantity: self::getIntOrNull($row, ProductStockSchemaMysql::QUANTITY) ?? 0,
            stockMinimum: self::getIntOrNull($row, ProductStockSchemaMysql::STOCK_MINIMUM) ?? 0,
            stockMaximum: self::getIntOrNull($row, ProductStockSchemaMysql::STOCK_MAXIMUM) ?? 0,
        );
    }
}
