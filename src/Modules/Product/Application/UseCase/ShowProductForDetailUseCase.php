<?php

namespace Src\Modules\Product\Application\UseCase;

use Config\AppConfig;
use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

/**
 * DemoProductForDetail — USE CASE FACTICE pour aperçu frontend
 *
 * À utiliser UNIQUEMENT en développement pour tester la vue sans base de données.
 * Branche dans le controller à la place de ShowProductForDetail le temps du dev,
 * puis supprime cette classe une fois l'entité réelle branchée.
 *
 * Usage dans le controller :
 *   $this->render('Product/detail.twig', ['model' => (new DemoProductForDetail())->execute()]);
 */
final class ShowProductForDetailUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): ResultUseCase
    {
        $product = $this->productRepo->findOne(new ProductQuery(slug: $slug, withAttributes: true, withStock: true, withCategories: true, withImages: true));

        if ($product === null) {
            return ResultUseCase::failure("Produit introuvable.", 'PRODUCT_NOT_FOUND');
        }

        $vm = new ProductDetailViewModel();

        // — Identification ------------------------------------------------
        $vm->name          = $product->name ?? "Aucun nom";
        $vm->slug          = $product->slug ?? "Aucun slug";
        $vm->reference     = $product->reference ?? "Aucune référence";
        $vm->categories = $product->categories ?? "Aucune catégorie";

        // — Textes --------------------------------------------------------
        $vm->subtitle = $product->subtitle ?? "Aucun sous titre";
        $vm->description = $product->description ?? "Aucune description";
        $vm->meta_description = $product->metaDescription ?? "Aucune meta description";

        // — Disponibilité -------------------------------------------------
        $vm->quantity = $product->stockProduct->quantity;
        $vm->available = $vm->quantity > 0 ? true : false;

        // — Médias --------------------------------------------------------
        // Remplace les chemins par tes vraies images de test
        $vm->images = [
            ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . $product->images[0]->filePath],
            ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'drzwi-z-20251008-145812-68e67c04e5c6c.webp'],
            ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'pf152wg-20251008-150215-68e67cf7e5401.webp'],
            ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'sl600ttevo-20251008-150030-68e67c8e6f45c.webp'],
            ['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'procural-pe50-20251008-144025-68e677d923203.webp'],
        ];

        // — Specs ---------------------------------------------------------
        $vm->specs = [
            ['label' => 'Référence',              'value' => 'PE78N-PFF-001'],
            ['label' => 'Type',                   'value' => 'Porte-fenêtre à frappe'],
            ['label' => 'Matériau',               'value' => 'Aluminium'],
            ['label' => 'Fabricant',              'value' => 'PROCURAL'],
            ['label' => 'Profilé aluminium',      'value' => 'EN AW-6060 – T6/T66 selon PN-EN 573-3'],
            ['label' => 'Joints',                 'value' => 'Caoutchouc EPDM – DIN 7863 / ISO 3302-01 E2'],
            ['label' => 'Dimensions max vantail', 'value' => 'L 1 700 × H 2 200 mm  |  L 1 300 × H 3 000 mm'],
            ['label' => 'Poids max vantail',      'value' => '120 kg'],
            ['label' => 'Plage de vitrage',       'value' => '22 – 60 mm'],
            ['label' => 'Isolation thermique',    'value' => 'Barrettes 34 mm – dormants & vantaux'],
            ['label' => 'Seuil',                  'value' => 'Au choix (bas, à encastrer, PMR)'],
            ['label' => 'Compatibilité',          'value' => 'Liaison possible avec gammes PROCURAL PE78N'],
        ];

        // — Features (bullets points forts) --------------------------------
        $vm->features = $product->features;

        // — Documents téléchargeables -------------------------------------
        $vm->documents = [
            ['label' => 'Fiche technique PDF',      'url' => '/uploads/docs/pe78n-fiche-technique.pdf'],
            ['label' => 'Plan de pose',              'url' => '/uploads/docs/pe78n-plan-pose.pdf'],
            ['label' => 'Certificat de performance', 'url' => '/uploads/docs/pe78n-certificat.pdf'],
        ];

        // — Produits similaires -------------------------------------------
        $vm->related_products = [
            [
                'name'          => "PROCURAL PE78N – Fenêtre oscillo-battante",
                'slug'          => "procural-pe78n-fenetre-oscillo-battante",
                'category_name' => "Fenêtres aluminium",
                'images'        => [['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'drzwi-z-20251008-145812-68e67c04e5c6c.webp']],
            ],
            [
                'name'          => "PROCURAL PE68 – Coulissant grande baie",
                'slug'          => "procural-pe68-coulissant-grande-baie",
                'category_name' => "Coulissants aluminium",
                'images'        => [['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'pf152wg-20251008-150215-68e67cf7e5401.webp']],
            ],
            [
                'name'          => "PROCURAL PE55 – Porte d'entrée aluminium",
                'slug'          => "procural-pe55-porte-entree",
                'category_name' => "Portes aluminium",
                'images'        => [['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'sl600ttevo-20251008-150030-68e67c8e6f45c.webp']],
            ],
            [
                'name'          => "PROCURAL PE78N – Fixe latéral",
                'slug'          => "procural-pe78n-fixe-lateral",
                'category_name' => "Vitrages fixes aluminium",
                'images'        => [['filePath' => AppConfig::getConst("URL_PATH_UPLOADS") . "img/products/large/" . 'procural-pe50-20251008-144025-68e677d923203.webp']],
            ],
        ];

        return ResultUseCase::success($vm);
    }
}
