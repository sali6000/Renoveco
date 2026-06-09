<?php

namespace Src\Modules\Product\Domain\Entity;

use Src\Modules\Category\Domain\Entity\Category;
use Src\Database\SchemaMysql;
use Core\Database\BaseModel;
use DateTime;

class Product extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string    $_reference,
        private string    $_slug,
        private string    $_name,
        private bool      $_isActive          = true,

        // Optionnels
        private ?int      $_id                = null,
        private ?string   $_description       = null,
        private ?string   $_composition       = null,
        private ?string   $_useFor            = null,
        private ?int      $_defaultSupplierId = null,
        private ?DateTime $_createdAt         = null,
        private ?DateTime $_updatedAt         = null,
        private ?string   $_subtitle          = null,
        private ?string   $_metaDescription   = null,

        // Listes
        /** Stocké en JSON dans la base de données */
        private array     $_features          = [],
        /** @var ProductImage[] */
        private array    $_images            = [],
        /** @var Category[] */
        private array    $_categories        = [],
        /** @var ProductAttribute[] */
        private array    $_attributes        = [],
        private ?ProductStock  $_stockProduct = null
    ) {}

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
        set(?string $value) => $this->_composition = $value;
    }

    public ?string $useFor {
        get => $this->_useFor;
        set(?string $value) => $this->_useFor = $value;
    }

    public ?int $defaultSupplierId {
        get => $this->_defaultSupplierId;
        set(?int $value) => $this->_defaultSupplierId = $value;
    }

    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $value) => $this->_createdAt = $value;
    }

    public ?DateTime $updatedAt {
        get => $this->_updatedAt;
        set(?DateTime $value) => $this->_updatedAt = $value;
    }

    public ?string $subtitle {
        get => $this->_subtitle;
        set(?string $value) => $this->_subtitle = $value;
    }

    public ?string $metaDescription {
        get => $this->_metaDescription;
        set(?string $value) => $this->_metaDescription = $value;
    }

    /** JSON */
    public array $features {
        get => $this->_features;
        set(array $values) => $this->_features = $values;
    }

    /** @var ProductImage[] */
    public array $images {
        get => $this->_images;
        set(array $values) => $this->_images = $values;
    }

    /** @var Category[] */
    public array $categories {
        get => $this->_categories;
        set(array $values) => $this->_categories = $values;
    }

    /** @var ProductAttribute[] */
    public array $attributes {
        get => $this->_attributes;
        set(array $value) => $this->_attributes = $value;
    }

    public ProductStock $stockProduct {
        get => $this->_stockProduct;
        set(ProductStock $value) => $this->_stockProduct = $value;
    }

    // ==========================================================
    // Fonctionnalités
    // ==========================================================

    public function addImage(ProductImage $image): void
    {
        // empêche les doublons (sécurité)
        foreach ($this->_images as $existing) {
            if ($existing->id === $image->id) {
                return;
            }
        }
        $this->_images[] = $image;
    }

    public function addCategory(Category $category): void
    {
        $this->_categories[] = $category;
    }

    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _reference: self::getString($row, SchemaMysql::PRODUCT_REFERENCE),
            _slug: self::getString($row, SchemaMysql::PRODUCT_SLUG),
            _name: self::getString($row, SchemaMysql::PRODUCT_NAME),
            _isActive: self::getBoolOrFalse($row, SchemaMysql::PRODUCT_IS_ACTIVE),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, SchemaMysql::PRODUCT_ID),
            _description: self::getStringOrNull($row, SchemaMysql::PRODUCT_DESCRIPTION),
            _composition: self::getStringOrNull($row, SchemaMysql::PRODUCT_COMPOSITION),
            _useFor: self::getStringOrNull($row, SchemaMysql::PRODUCT_USE_FOR),
            _createdAt: self::getDateOrNull($row, SchemaMysql::USER_CREATED_AT),
            _updatedAt: self::getDateOrNull($row, SchemaMysql::PRODUCT_UPDATED_AT),
            _subtitle: self::getStringOrNull($row, SchemaMysql::PRODUCT_SUBTITLE),
            _metaDescription: self::getStringOrNull($row, SchemaMysql::PRODUCT_META_DESCRIPTION),
            _stockProduct: self::getMappedOrNull($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_STOCK_PRODUCT), [ProductStock::class, 'fromArray']),

            // Listes ([])
            _features: self::getJsonOrEmpty($row, SchemaMysql::PRODUCT_FEATURES),
            _images: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_PRODUCT_IMAGE), [ProductImage::class, 'fromArray']),
            _attributes: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_ATTRIBUTE), [ProductAttribute::class, 'fromArray']),
            _categories: self::getMappedOrEmpty($row, SchemaMysql::fieldTable(SchemaMysql::TABLE_CATEGORY), [Category::class, 'fromArray'])
        );
    }
}
