<?php

namespace Src\Modules\Stock\Entity;

use Src\Modules\Category\Entity\Category;
use Src\Database\SchemaMysql;
use Core\Database\BaseModel;
use DateTime;

class Stock extends BaseModel
{
    // ==========================================================
    // Propriétés 11
    // ==========================================================
    //------------
    // Obligatoires 4
    //------------
    private string $_name;
    private string $_slug;
    private string $_reference;
    private bool $_isActive = true;

    //------------
    // Optionnelles 7
    //------------
    private ?int $_id = null;
    private ?string $_description = null;
    private ?string $_composition = null;
    private ?string $_useFor = null;
    private ?int $_defaultSupplierId = null;
    private ?DateTime $_createdAt = null;
    private ?DateTime $_updatedAt = null;

    // ==========================================================
    // Relationnelles (2)
    // ==========================================================
    /** @var ProductImage[] */
    private ?array $_images = [];

    /** @var Category[] */
    private ?array $_categories = [];

    // ==========================================================
    // Constructeur
    // ==========================================================
    public function __construct(
        string $name,
        string $slug,
        string $reference,
        bool $isActive,
        ?int $id = null,
        ?string $description = null,
        ?array $images = [],
        ?array $categories = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->images = $images;
        $this->categories = $categories;
        $this->isActive = $isActive;
        $this->reference = $reference;
    }

    // ==========================================================
    // = GETTERS / SETTERS 11
    // ==========================================================
    //------------
    // Obligatoires 4
    //------------
    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public string $slug {
        get => $this->_slug ?? '';
        set(string $value) => $this->_slug = $value ?? '';
    }

    public string $reference {
        get => $this->_reference ?? '';
        set(string $value) => $this->_reference = $value ?? '';
    }

    public bool $isActive {
        get => $this->_isActive;
        set(bool $value) => $this->_isActive = $value;
    }

    //------------
    // Optionnelles 7
    //------------
    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $description {
        get => $this->_description ?? '';
        set(?string $value) => $this->_description = $value ?? '';
    }

    public ?string $composition {
        get => $this->_composition ?? '';
        set(?string $value) => $this->_composition = $value ?? '';
    }

    public ?string $useFor {
        get => $this->_useFor ?? '';
        set(?string $value) => $this->_useFor = $value ?? '';
    }

    public ?int $defaultSupplierId {
        get => $this->_defaultSupplierId;
        set(?int $values) => $this->_defaultSupplierId = $values;
    }
    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $values) => $this->_createdAt = $values;
    }

    public ?DateTime $updatedAt {
        get => $this->_updatedAt;
        set(?DateTime $values) => $this->_updatedAt = $values;
    }

    // ==========================================================
    // = (RELATIONS) GETTERS / SETTERS (Hook)
    // ==========================================================
    public ?array $categories {
        get => $this->_categories;
        set(?array $values) => $this->_categories = $values;
    }

    public function addCategory(array|Category $category): void
    {
        $this->_categories[] = $category instanceof Category
            ? $category
            : Category::fromArray($category);
    }

    public ?array $images {
        get => $this->_images;
        set(?array $values) => $this->_images = $values;
    }


    // ==========================================================
    // Product <= $row <= rows[] 
    // ==========================================================

    /**
     * Retourne un Produit composé depuis un array
     * @return Product
     */
    public static function fromArray(array $row): self
    {
        /*private ?int $_id = null;
    private ?string $_description = null;
    private ?string $_composition = null;
    private ?string $_useFor = null;
    private ?int $_defaultSupplierId = null;
    private ?DateTime $_createdAt = null;
    private ?DateTime $_updatedAt = null;*/

        $product = new self(
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_NAME)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_SLUG)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_REFERENCE)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IS_ACTIVE)] ?? false,
        );

        $product->id = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_ID)] ?? null;
        $product->description  = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_DESCRIPTION)] ?? null;
        $product->composition = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_COMPOSITION)] ?? null;
        $product->useFor = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_USE_FOR)] ?? null;
        $product->defaultSupplierId = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_DEFAULT_SUPPLIER_ID)] ?? null;
        $product->createdAt = self::toDateTime($row[SchemaMysql::fieldProperty(SchemaMysql::USER_CREATED_AT)] ?? null);
        $product->updatedAt = self::toDateTime($row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_UPDATED_AT)] ?? null);

        if (!empty($row['images']))
            $product->images = array_map([ProductImage::class, 'fromArray'], $row['images']);

        if (!empty($row['categories']))
            $product->categories = array_map([Category::class, 'fromArray'], $row['categories']);

        return $product;
    }
}
