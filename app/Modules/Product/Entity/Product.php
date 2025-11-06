<?php

namespace App\Modules\Product\Entity;

class Product
{
    // ==========================================================
    // = PROPERTIES
    // ==========================================================

    /**
     * Identifiant unique du produit (clé primaire auto-incrémentée)
     */
    private ?int $_id = null;

    /**
     * Nom commercial du produit
     */
    private string $_name;

    /**
     * Slug SEO du produit (généré à partir du nom, ex: "chaussure-nike-air")
     */
    private ?string $_slug = null;

    /**
     * Brève description du produit, visible sur la fiche produit
     */
    private ?string $_description = null;

    /** @var array<int, array{file_path: string, alt_text: ?string}> */
    private array $_images = [];

    private array $_categories = [];

    // ==========================================================
    // = CONSTRUCTOR
    // ==========================================================

    /**
     * Initialise un nouveau produit avec un identifiant et un nom
     *
     * @param int|null $id Identifiant unique (ou null si non persisté)
     * @param string   $name Nom du produit
     */
    public function __construct(?int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
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

    public string $name {
        get => $this->_name;
        set(string $value) {
            $this->_name = $value;
        }
    }

    public ?string $slug {
        get => $this->_slug ?? '';
        set(?string $value) {
            $this->_slug = $value ?? '';
        }
    }

    public ?string $description {
        get => $this->_description ?? '';
        set(?string $value) {
            $this->_description = $value ?? '';
        }
    }

    public array $images {
        get => $this->_images;
        set(array $values) {
            $this->_images = [];
            foreach ($values as $image) {
                $this->_images[] = $image instanceof ProductImage
                    ? $image
                    : ProductImage::fromArray($image);
            }
        }
    }

    public array $categories {
        get => $this->_categories;
        set(array $values) {
            $this->_categories = [];
            foreach ($values as $category) {
                $this->_categories[] = $category instanceof ProductCategory
                    ? $category
                    : ProductCategory::fromArray($category);
            }
        }
    }

    public function addImage(array|ProductImage $image): void
    {
        $this->_images[] = $image instanceof ProductImage
            ? $image
            : ProductImage::fromArray($image);
    }


    // ==========================================================
    // = FROM ARRAY
    // ==========================================================

    /**
     * Retourne un Produit composé depuis un array
     * nécessite une colonne ['id'] et ['name'] dans array $data
     *
     * @return Product
     */
    public static function fromArray(array $data): self
    {
        foreach (['id', 'name'] as $required) {
            if (!array_key_exists($required, $data)) {
                throw new \InvalidArgumentException(
                    "Clé '$required' manquante dans Product::fromArray()"
                );
            }
        }

        $product = new self($data['id'], $data['name']);

        // affectations directes via hooks
        $product->slug = $data['slug'] ?? null;
        $product->description = $data['description'] ?? null;
        $product->categories = $data['categories'] ?? [];


        // Initialiser la propriété images à un tableau vide
        $product->images = [];

        // Cas 1 : si on a un tableau d'images (ex: jointure multiple)
        if (!empty($data['images']) && is_array($data['images'])) {
            // Nettoyer et filtrer les images valides
            $product->images = array_values(array_filter($data['images'], function ($img) {
                return !empty($img['file_path']);
            }));
        }

        // Cas 2 : si on a seulement une seule image (via colonnes SQL)
        elseif (!empty($data['file_path'])) {
            $product->addImage([
                'file_path' => $data['file_path'],
                'alt_text'  => $data['alt_text'] ?? null
            ]);
        }
        return $product;
    }
}
