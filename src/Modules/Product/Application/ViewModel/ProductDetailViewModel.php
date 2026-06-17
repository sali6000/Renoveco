<?php

namespace Src\Modules\Product\Application\ViewModel;

/**
 * ViewModel — Produit Détail
 *
 * Construit depuis l'entité Product (ou un DTO de sortie du use case),
 * expose exactement ce dont la vue a besoin — rien de plus.
 *
 * La vue ne connaît pas l'entité domain : elle ne reçoit que ce ViewModel.
 * 
 * Exemples:
 * $available      // renommé, sémantique utilisateur
 * $category_name  // string aplati, plus de tableau
 * $category_slug  // string aplati
 * $images         // array simplifié [['filePath' => '...']]
 * $specs          // construit depuis composition + useFor + reference…
 * $features       // n'existe pas dans l'entité, construit à part
 * 
 */
final class ProductDetailViewModel
{
    // -------------------------------------------------------------------------
    // Identification
    // -------------------------------------------------------------------------
    public string $name;
    public string $slug;
    public string $reference;
    public string $category_slug;
    public string $category_name;


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
    public int $quantity;

    // -------------------------------------------------------------------------
    // Médias
    // -------------------------------------------------------------------------
    public string $image_main;
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

    /** @var ProductAttribute[] */
    public array $attributes;

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
}
