<?php

namespace Src\Modules\Product\Application\UseCase;

use Config\AppConfig;
use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowProductForDetailUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): ResultUseCase
    {
        $product = $this->productRepo->findProduct(new ProductQuery(bySlug: $slug, withAttributes: true, withStock: true, withCategories: true, withImages: true));

        if ($product === null) {
            return ResultUseCase::failure("Produit introuvable.", 'PRODUCT_NOT_FOUND');
        }

        $vm = new ProductDetailViewModel();

        // — Identification ------------------------------------------------
        $vm->name          = $product->name ?? "Aucun nom";
        $vm->slug          = $product->slug ?? "Aucun slug";
        $vm->reference     = $product->reference ?? "Aucune référence";
        $vm->category_name = $product->categories[0]->name ?? "Aucune catégorie";
        $vm->category_slug = $product->categories[0]->slug ?? '';
        $vm->attributes = $product->attributes ?? ["aucun"];

        // — Description --------------------------------------------------------
        $vm->subtitle = $product->subtitle ?? "Aucun sous titre";
        $vm->description = $product->description ?? "Aucune description";
        $vm->meta_description = $product->metaDescription ?? "Aucune meta description";

        // — Disponibilité -------------------------------------------------
        $vm->quantity = $product->stockProduct->quantity;
        $vm->available = $vm->quantity > 0;

        // — Médias --------------------------------------------------------
        // Image principal
        $vm->image_main = $product->getMainImage()
            ? AppConfig::getConst('URL_PATH_UPLOADS') . "img/products/large/" . $product->getMainImage()->filePath
            : null;

        // Autres images
        $vm->images = [];
        foreach ($product->images as $image) {
            $vm->images[] = ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . $image->filePath];
        }

        // — Specs ---------------------------------------------------------
        $vm->specs = [];
        foreach ($product->attributes as $attribute) {
            $vm->specs[] = ['label' => $attribute->attributeName, 'value' => $attribute->value];
        }

        // — Features (bullets points forts) --------------------------------
        $vm->features = $product->features;

        // — Produits similaires -------------------------------------------
        // 1. Récupérer 5 produits de la catégorie du produit actuel
        $related_products = $this->productRepo->findProducts(new ProductQuery(byCategory: $product->categories[0], withImages: true, limit: 5));

        // 2. Supprimer le produit actuel de la liste
        $related_products = array_filter(
            $related_products,
            fn(Product $p) => $p->slug !== $product->slug
        );

        // 3. Assigner le chemin image complet aux produits de la liste
        $vm->related_products = array_map(
            fn(Product $p) => [
                'name'      => $p->name,
                'slug'      => $p->slug,
                'image'     => $p->getMainImage()
                    ? AppConfig::getConst('URL_PATH_UPLOADS') . "img/products/large/" . $p->getMainImage()->filePath
                    : null,
            ],
            $related_products
        );

        /* — Documents téléchargeables -------------------------------------
        $vm->documents = [
            ['label' => 'Fiche technique PDF',      'url' => '/uploads/docs/pe78n-fiche-technique.pdf'],
            ['label' => 'Plan de pose',              'url' => '/uploads/docs/pe78n-plan-pose.pdf'],
            ['label' => 'Certificat de performance', 'url' => '/uploads/docs/pe78n-certificat.pdf'],
        ];*/

        return ResultUseCase::success($vm);
    }
}
