<?php

namespace Src\Modules\Category\Domain\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;
use Src\Modules\Product\Domain\Entity\Product;

class Category extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string $_name,

        // Optionnels
        private ?string $_slug = null,
        private ?string $_description = null,
        private ?int $_id = null,
        private ?int $_parentId = null,
        private ?Category $_parent = null,

        // Listes
        /** @var Category[] */
        private array $_childrens = [],
        /** @var Product[] */
        private array $_products = [],
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $slug {
        get => $this->_slug;
        set(?string $value) => $this->_slug = $value;
    }

    public ?string $description {
        get => $this->_description;
        set(?string $value) => $this->_description = $value;
    }

    public ?int $parentId {
        get => $this->_parentId;
        set(?int $value) => $this->_parentId = $value;
    }

    public ?Category $parent {
        get => $this->_parent;
        set(?Category $value) => $this->_parent = $value;
    }

    /** @var Category[] */
    public array $childrens {
        get => $this->_childrens;
        set(array $value) => $this->_childrens = $value;
    }

    public array $products {
        get => $this->_products;
        set(array $value) => $this->_products = $value;
    }



    // ==========================================================
    // Fonctionnalités
    // ==========================================================

    public function addProduct(Product $product): void
    {
        // empêche les doublons (sécurité)
        foreach ($this->_products as $existing) {
            if ($existing->id === $product->id) return;
        }
        $this->_products[] = $product;
    }

    public function addChild(Category $child): void
    {
        // empêche les doublons (sécurité)
        foreach ($this->_childrens as $existing) {
            if ($existing->id === $child->id) return;
        }

        $this->_childrens[] = $child;
    }

    public function hasChildren(): bool
    {
        return !empty($this->_childrens);
    }

    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): ?self
    {
        return new self(

            // Obligatoires
            _name: self::getString($row, SchemaMysql::CATEGORY_NAME),

            // Optionnelles (nullable)
            _slug: self::getStringOrNull($row, SchemaMysql::CATEGORY_SLUG),
            _description: self::getStringOrNull($row, SchemaMysql::CATEGORY_DESCRIPTION),
            _id: self::getIntOrNull($row, SchemaMysql::CATEGORY_ID),
            _parentId: self::getIntOrNull($row, SchemaMysql::CATEGORY_PARENT_ID),

            // Listes ([])
            _products: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_PRODUCT), [Product::class, 'fromArray'])
        );
    }
}
