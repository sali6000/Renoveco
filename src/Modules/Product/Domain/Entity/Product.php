<?php

namespace Src\Modules\Product\Domain\Entity;

use Src\Modules\Category\Domain\Entity\Category;
use Src\Database\SchemaMysql;
use Core\Database\BaseModel;
use DateTime;

/**
 * Product (entité domain) — reflète la DB et les règles métier
 */
class Product extends BaseModel
{
    // ==========================================================
    // CONSTRUCTEUR ET PROPRIETES
    // ==========================================================
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
        private array     $_features          = [], // Json<features>[]
        /** @var ProductImage[] */
        private array    $_images            = [],
        /** @var Category[] */
        private array    $_categories        = [],
        /** @var ProductAttribute[] */
        private array    $_attributes        = [],
    ) {}


    // ==========================================================
    // HOOKS (attributes hooks PHP 8.4)
    // ==========================================================

    // --- NOT NULL ---
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

    // --- NULL ---
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

    // --- EMPTY ---
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

    public array $features {
        get => $this->_features;
        set(array $values) => $this->_features = $values;
    }

    // ==========================================================
    // MUTATION DES RELATIONS
    // ==========================================================
    public function addImage(array|ProductImage $image): void
    {
        $this->_images[] = $image instanceof ProductImage
            ? $image
            : ProductImage::fromArray($image);
    }

    public function addCategory(array|Category $category): void
    {
        $this->_categories[] = $category instanceof Category
            ? $category
            : Category::fromArray($category);
    }

    // ==========================================================
    // FACTORY — hydratation depuis un row DB
    // ==========================================================
    public static function fromArray(array $row): self
    {
        return new self(
            _reference: self::getString($row, SchemaMysql::PRODUCT_REFERENCE),
            _slug: self::getString($row, SchemaMysql::PRODUCT_SLUG),
            _name: self::getString($row, SchemaMysql::PRODUCT_NAME),
            _isActive: self::getBoolOrFalse($row, SchemaMysql::PRODUCT_IS_ACTIVE),
            _id: self::getIntOrNull($row, SchemaMysql::PRODUCT_ID),
            _description: self::getStringOrNull($row, SchemaMysql::PRODUCT_DESCRIPTION),
            _composition: self::getStringOrNull($row, SchemaMysql::PRODUCT_COMPOSITION),
            _useFor: self::getStringOrNull($row, SchemaMysql::PRODUCT_USE_FOR),
            _createdAt: self::getDateOrNull($row, SchemaMysql::USER_CREATED_AT),
            _updatedAt: self::getDateOrNull($row, SchemaMysql::PRODUCT_UPDATED_AT),
            _features: self::getJsonOrEmpty($row, SchemaMysql::PRODUCT_FEATURES),
            _subtitle: self::getStringOrNull($row, SchemaMysql::PRODUCT_SUBTITLE),
            _metaDescription: self::getStringOrNull($row, SchemaMysql::PRODUCT_META_DESCRIPTION),

            // Passe par le hook (set)
            _images: self::getMappedOrEmpty($row, 'images', [ProductImage::class, 'fromArray']),
            _attributes: self::getMappedOrEmpty($row, 'attributes', [ProductAttribute::class, 'fromArray']),
            _categories: self::getMappedOrEmpty($row, 'categories', [Category::class, 'fromArray'])
        );
    }
}
