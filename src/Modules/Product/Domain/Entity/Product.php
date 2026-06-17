<?php

namespace Src\Modules\Product\Domain\Entity;

use Src\Modules\Category\Domain\Entity\Category;
use Core\Database\BaseModel;
use DateTime;
use Src\Modules\Attribute\Infrastructure\Schema\AttributeSchemaMysql;
use Src\Modules\Category\Infrastructure\Schema\CategorySchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductImageSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductStockSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

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

    public function getMainImage(): ?ProductImage
    {
        foreach ($this->images as $image) {
            if ($image->isMain) return $image;
        }
        return $this->images[0] ?? null;
    }

    // ==========================================================
    // Hydratation (Entity <- array)
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _reference: self::getString($row, ProductSchemaMysql::REFERENCE),
            _slug: self::getString($row, ProductSchemaMysql::SLUG),
            _name: self::getString($row, ProductSchemaMysql::NAME),
            _isActive: self::getBoolOrFalse($row, ProductSchemaMysql::IS_ACTIVE),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, ProductSchemaMysql::ID),
            _description: self::getStringOrNull($row, ProductSchemaMysql::DESCRIPTION),
            _composition: self::getStringOrNull($row, ProductSchemaMysql::COMPOSITION),
            _useFor: self::getStringOrNull($row, ProductSchemaMysql::USE_FOR),
            _createdAt: self::getDateOrNull($row, ProductSchemaMysql::CREATED_AT),
            _updatedAt: self::getDateOrNull($row, ProductSchemaMysql::UPDATED_AT),
            _subtitle: self::getStringOrNull($row, ProductSchemaMysql::SUBTITLE),
            _metaDescription: self::getStringOrNull($row, ProductSchemaMysql::META_DESCRIPTION),
            _stockProduct: self::getMappedOrNull($row, HelperSchemaMysql::fieldTable(ProductStockSchemaMysql::TABLE), [ProductStock::class, 'fromArray']),

            // Listes ([])
            _features: self::getJsonOrEmpty($row, ProductSchemaMysql::FEATURES),
            _images: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(ProductImageSchemaMysql::TABLE), [ProductImage::class, 'fromArray']),
            _attributes: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(AttributeSchemaMysql::TABLE), [ProductAttribute::class, 'fromArray']),
            _categories: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(CategorySchemaMysql::TABLE), [Category::class, 'fromArray'])
        );
    }
}
