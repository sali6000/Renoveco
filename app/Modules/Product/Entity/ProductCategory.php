<?php

namespace App\Modules\Product\Entity;

class ProductCategory
{
    private ?int $_id = null;

    private ?int $_parentId = null;

    private string $_name;

    private ?string $_slug = null;

    private ?string $_description = null;

    // ---------------------------
    // Relations
    // ---------------------------

    private ?ProductCategory $_parent = null;

    private array $_products = [];

    public function __construct(
        string $name,
        ?string $slug = null,
        ?string $description = null,
        ?int $parentId = null
    ) {
        $this->parentId = $parentId;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
    }


    // ==========================================================
    // = GETTERS / SETTERS (Hook)
    // ==========================================================


    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }

    public ?int $parentId {
        get => $this->_parentId;
        set(?int $value) {
            $this->_parentId = $value;
        }
    }

    public ?ProductCategory $parent {
        get => $this->_parent;
        set(?ProductCategory $value) {
            $this->_parent = $value;
        }
    }

    public string $name {
        get => $this->_name;
        set(string $value) {
            $this->_name = $value;
        }
    }


    public string $slug {
        get => $this->_slug;
        set(string $value) {
            $this->_slug = $value;
        }
    }


    public ?string $description {
        get => $this->_description;
        set(?string $value) {
            $this->_description = $value;
        }
    }

    public function addProduct(Product $product): void
    {
        $this->_products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->_products;
    }
    // ---------------------------
    // Conversion (utile pour API / debug)
    // ---------------------------
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];
    }


    public static function fromArray(array $data): self
    {
        // Ex: new ProductCategory(5, 'Informatique', 'informatique');
        return new self(
            $data['name'] ?? '',
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['parent_id'] ?? null
        );
    }
}
