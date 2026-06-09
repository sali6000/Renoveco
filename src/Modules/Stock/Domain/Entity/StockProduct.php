<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;
use Src\Modules\Product\Domain\Entity\Product;

class StockProduct extends BaseModel
{
    // ==========================================================
    // CONSTRUCTEUR ET PROPRIETES
    // ==========================================================
    public function __construct(

        // Obligatoires
        /** @var Product */
        private Product $_product,
        private int $_quantity = 0,
        private int $_stock_minimum = 0,

        // Optionnelles
        private ?int $_id = null,
        private ?int $_stock_maximum = null,
        /** @var StockProductLocation[] */
        private array $_stockProductLocation = [],
        /** @var StockLocation[] */
        private array $_stockLocation = []
    ) {}

    public Product $product {
        get => $this->_product;
        set(Product $value) => $this->_product = $value;
    }

    public int $quantity {
        get => $this->_quantity;
        set(int $value) => $this->_quantity = $value;
    }
    public int $stock_minimum {
        get => $this->_stock_minimum;
        set(int $value) => $this->_stock_minimum = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?int $stock_maximum {
        get => $this->_stock_maximum;
        set(?int $value) => $this->_stock_maximum = $value;
    }

    /** @var StockProductLocation[] */
    public array $stockProductLocation {
        get => $this->_stockProductLocation;
        set(array $value) => $this->_stockProductLocation = $value;
    }

    /** @var StockLocation[] */
    public array $stockLocation {
        get => $this->_stockLocation;
        set(array $value) => $this->_stockLocation = $value;
    }

    // ==========================================================
    // FACTORY — hydratation depuis un row DB
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(
            _product: self::getMappedOrNull($row, 'product', [Product::class, 'fromArray']) ?? throw new \RuntimeException('Missing product relation'),
            _quantity: self::getInt($row, SchemaMysql::STOCK_PRODUCT_QUANTITY),
            _stock_minimum: self::getInt($row, SchemaMysql::STOCK_PRODUCT_STOCK_MINIMUM),
            _id: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_ID),
            _stock_maximum: self::getIntOrNull($row, SchemaMysql::STOCK_PRODUCT_STOCK_MAXIMUM),
            _stockProductLocation: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_STOCK_PRODUCT_LOCATION), [StockProductLocation::class, 'fromArray']),
            _stockLocation: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_STOCK_LOCATION), [StockLocation::class, 'fromArray'])
        );
    }
}
