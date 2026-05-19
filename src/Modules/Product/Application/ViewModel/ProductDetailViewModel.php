<?php

namespace Src\Modules\Product\Application\ViewModel;

/**
 * ViewModel — Produit Détail
 *
 * Construit depuis l'entité Product (ou un DTO de sortie du use case),
 * expose exactement ce dont la vue a besoin — rien de plus.
 *
 * La vue ne connaît pas l'entité domain : elle ne reçoit que ce ViewModel.
 */
final class ProductDetailViewModel
{
    // -------------------------------------------------------------------------
    // Identification
    // -------------------------------------------------------------------------
    public string $name;
    public string $slug;
    public string $reference;
    public string $category_name;
    public string $category_slug;

    // -------------------------------------------------------------------------
    // Textes
    // -------------------------------------------------------------------------
    public string $subtitle;
    public string $description;
    public ?string $meta_description;

    // -------------------------------------------------------------------------
    // Disponibilité
    // -------------------------------------------------------------------------
    public bool $available;

    // -------------------------------------------------------------------------
    // Médias
    // -------------------------------------------------------------------------
    /** @var array<int, array{filePath: string}> */
    public array $images;

    // -------------------------------------------------------------------------
    // Fiche technique — générée par buildSpecs(), jamais hardcodée dans la vue
    // -------------------------------------------------------------------------
    /** @var array<int, array{label: string, value: string}> */
    public array $specs;

    // -------------------------------------------------------------------------
    // Points forts (bullets)
    // -------------------------------------------------------------------------
    /** @var string[] */
    public array $features;

    // -------------------------------------------------------------------------
    // Documents téléchargeables (PDF, plans…)
    // -------------------------------------------------------------------------
    /** @var array<int, array{label: string, url: string}> */
    public array $documents;

    // -------------------------------------------------------------------------
    // Produits similaires
    // -------------------------------------------------------------------------
    /** @var array<int, array{name: string, slug: string, category_name: string, images: array}> */
    public array $related_products;

    // -------------------------------------------------------------------------
    // Construction depuis une entité / DTO
    // -------------------------------------------------------------------------

    /**
     * Point d'entrée unique : le use case appelle fromEntity() et renvoie
     * le ViewModel à la vue. Le controller ne touche pas aux données.
     *
     * @param object $product  Entité ou DTO de sortie du domaine
     * @param array  $related  Tableau d'entités produits similaires (peut être vide)
     */
    public static function fromEntity(object $product, array $related = []): self
    {
        $vm = new self();

        // — Identification ------------------------------------------------
        $vm->name          = $product->getName();
        $vm->slug          = $product->getSlug();
        $vm->reference     = $product->getReference();
        $vm->category_name = $product->getCategory()->getName();
        $vm->category_slug = $product->getCategory()->getSlug();

        // — Textes --------------------------------------------------------
        $vm->subtitle         = $product->getSubtitle() ?? '';
        $vm->description      = $product->getDescription();
        $vm->meta_description = $product->getMetaDescription();

        // — Disponibilité -------------------------------------------------
        $vm->available = $product->isAvailable();

        // — Médias --------------------------------------------------------
        $vm->images = array_map(
            fn($img) => ['filePath' => $img->getFilePath()],
            $product->getImages()->toArray()
        );

        // — Specs (ordre d'affichage contrôlé ici, pas dans la vue) -------
        $vm->specs = self::buildSpecs($product);

        // — Features ------------------------------------------------------
        $vm->features = $product->getFeatures() ?? [];

        // — Documents -----------------------------------------------------
        $vm->documents = array_map(
            fn($doc) => [
                'label' => $doc->getLabel(),
                'url'   => $doc->getUrl(),
            ],
            $product->getDocuments() ?? []
        );

        // — Produits similaires -------------------------------------------
        $vm->related_products = array_map(
            fn($p) => [
                'name'          => $p->getName(),
                'slug'          => $p->getSlug(),
                'category_name' => $p->getCategory()->getName(),
                'images'        => [['filePath' => $p->getImages()->first()->getFilePath()]],
            ],
            $related
        );

        return $vm;
    }

    // -------------------------------------------------------------------------
    // Méthode privée — construit le tableau de specs ordonné
    //
    // array_filter() élimine les champs null/vide → la vue itère sans condition
    // -------------------------------------------------------------------------
    private static function buildSpecs(object $product): array
    {
        return array_values(array_filter([
            ['label' => 'Référence',              'value' => $product->getReference()],
            ['label' => 'Type',                   'value' => $product->getType()],
            ['label' => 'Matériau',               'value' => $product->getMaterial()],
            ['label' => 'Fabricant',              'value' => $product->getManufacturer()],
            ['label' => 'Profilé aluminium',      'value' => $product->getAluminiumProfile()],
            ['label' => 'Joints',                 'value' => $product->getJoints()],
            ['label' => 'Dimensions max vantail', 'value' => $product->getMaxDimensions()],
            ['label' => 'Plage de vitrage',       'value' => $product->getGlazingRange()],
            ['label' => 'Poids max vantail',      'value' => $product->getMaxWeight() ? $product->getMaxWeight() . ' kg' : null],
            ['label' => 'Seuil',                  'value' => $product->getThreshold()],
        ], fn(array $spec): bool => $spec['value'] !== null && $spec['value'] !== ''));
    }
}
