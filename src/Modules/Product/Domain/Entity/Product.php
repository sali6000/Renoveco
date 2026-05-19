<?php

namespace Src\Modules\Product\Domain\Entity;

use Src\Modules\Category\Domain\Entity\Category;
use Src\Database\SchemaMysql;
use Core\Database\BaseModel;
use DateTime;

/**
 * Product (entité domain) — reflète la DB et les règles métier
 * 
 * Exemples:
 * $_isActive    // bool métier
 * $_categories  // array de Category (relation complète)
 * $_images      // array de ProductImage (objets complets)
 * $_composition // colonne DB
 * $_useFor      // colonne DB
 * 
 */

class Product extends BaseModel
{
    // ==========================================================
    // Propriétés
    // ==========================================================
    //------------
    // NOT NULL EN DB (par défaut)
    //------------
    private string $_reference;
    private string $_slug;
    private string $_name;
    private bool $_isActive;

    //------------
    // NULL en DB (par défaut)
    //------------
    private ?int $_id = null; // (auto incrémentation en base)
    private ?string $_description = null;
    private ?string $_composition = null;
    private ?string $_useFor = null;
    private ?int $_defaultSupplierId = null;
    private ?DateTime $_createdAt = null;
    private ?DateTime $_updatedAt = null;
    private ?string $_subtitle = null;
    private ?string $_metaDescription = null;
    private ?array $_images = []; // ProductImage[]
    private ?array $_categories = []; // Category[]

    public function __construct(
        string $reference,
        string $slug,
        string $name,
        bool $isActive = true,
        ?int $id = null,
        ?string $description = null,
        ?string $composition = null,
        ?string $useFor = null,
        ?int $defaultSupplierId = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?string $subtitle = null,
        ?string $metaDescription = null,
        array $images = [],
        array $categories = []
    ) {
        $this->reference = $reference;
        $this->slug = $slug;
        $this->name = $name;
        $this->isActive = $isActive;

        $this->id = $id;
        $this->description = $description;
        $this->composition = $composition;
        $this->useFor = $useFor;
        $this->defaultSupplierId = $defaultSupplierId;

        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->subtitle = $subtitle;
        $this->metaDescription = $metaDescription;

        $this->images = $images;
        $this->categories = $categories;
    }

    // ==========================================================
    // = GETTERS / SETTERS
    // ==========================================================
    //------------
    // Obligatoires
    //------------
    public string $reference {
        get => $this->_reference;
        set(string $value) => $this->_reference = $value;
    }

    public string $slug {
        get => $this->_slug;
        set(string $value) => $this->_slug = $value;
    }

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public bool $isActive {
        get => $this->_isActive;
        set(bool $value) => $this->_isActive = $value;
    }

    //------------
    // Optionnelles
    //------------
    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $description {
        get => $this->_description;
        set(?string $value) => $this->_description = $value;
    }

    public ?string $composition {
        get => $this->_composition;
        set(?string $value) => $this->_composition = $value ?? '';
    }

    public ?string $useFor {
        get => $this->_useFor;
        set(?string $value) => $this->_useFor = $value;
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

    public ?string $subtitle {
        get => $this->_subtitle;
        set(?string $value) => $this->_subtitle = $value;
    }

    public ?string $metaDescription {
        get => $this->_metaDescription;
        set(?string $value) => $this->_metaDescription = $value;
    }

    // ==========================================================
    // RELATIONS
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

    public function addImage(array|ProductImage $image): void
    {
        $this->_images[] = $image instanceof ProductImage
            ? $image
            : ProductImage::fromArray($image);
    }

    // ==========================================================
    // Product <= rows[] 
    // ==========================================================

    /**
     * Retourne un Produit composé depuis un array
     * @return Product
     */
    public static function fromArray(array $row): self
    {
        // Valeurs non nullables en base
        $product = new self(
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_REFERENCE)],
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_NAME)],
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_SLUG)],
            (bool) $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IS_ACTIVE)],
        );

        // Valeurs nullables en base
        $product->id = isset($row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_ID)])
            ? (int) $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_ID)]
            : null;

        $product->description = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_DESCRIPTION)] ?? null;
        $product->composition = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_COMPOSITION)] ?? null;
        $product->useFor = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_USE_FOR)] ?? null;
        $product->subtitle = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_SUBTITLE)] ?? null;
        $product->metaDescription = $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_META_DESCRIPTION)] ?? null;
        $product->createdAt = self::toDateTime($row[SchemaMysql::fieldProperty(SchemaMysql::USER_CREATED_AT)] ?? null);
        $product->updatedAt = self::toDateTime($row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_UPDATED_AT)] ?? null);

        // Relations
        $product->images = !empty($row['images'])
            ? array_map([ProductImage::class, 'fromArray'], $row['images'])
            : [];

        $product->categories = !empty($row['categories'])
            ? array_map([Category::class, 'fromArray'], $row['categories'])
            : [];

        return $product;
    }
}
