<?php

namespace App\Modules\Category\Domain\Entity;

use App\Database\SchemaMysql;
use App\Modules\Product\Domain\Entity\Product;

class Category
{
    private string $_name;
    private ?string $_slug = null;
    private ?string $_description = null;
    private ?int $_id = null;
    private ?int $_parentId = null;
    private ?Category $_parent = null;
    /** @var Category[] */
    private array $_children = [];
    /** @var Product[] */
    private array $_products = [];

    public function __construct(
        string $name,
        ?string $slug = null,
        ?string $description = null,
        ?int $id = null,
        ?int $parentId = null
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->id = $id;
        $this->parentId = $parentId;
    }

    // ==========================================================
    // = GETTERS / SETTERS
    // ==========================================================

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

    // ==========================================================
    // = CHILDREN MANAGEMENT
    // ==========================================================

    public function addChild(Category $child): void
    {
        // empêche les doublons (sécurité)
        foreach ($this->_children as $existing) {
            if ($existing->id === $child->id) {
                return;
            }
        }
        $this->_children[] = $child;
    }

    /**
     * @return Category[]
     */
    public function getChildren(): array
    {
        return $this->_children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->_children);
    }

    // ==========================================================
    // = PRODUCTS
    // ==========================================================

    public function addProduct(Product $product): void
    {
        $this->_products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->_products;
    }

    public static function fromArray(array $row): ?self
    {
        return new self(
            $row[SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_NAME)] ?? '',
            $row[SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_SLUG)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_DESCRIPTION)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_ID)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_PARENT_ID)] ?? null
        );
    }
}
