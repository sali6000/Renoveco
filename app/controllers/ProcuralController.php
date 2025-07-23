<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;
use App\Services\ProductService;

class ProcuralController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct();
        $this->productService = $productService;
    }

    /**
     * Affiche la liste des produits de type "Procural"
     */
    public function index()
    {
        try {
            $models = $this->productService->getAllProductsByFilter('isProcural', '1');

            $this->set('models', $models);
            $this->set('base_url_assets_img_materials', BASE_URL_PUBLIC_ASSETS_IMG_MATERIALS);

            $this->view('product/procural-list', $this->data);
        } catch (\Exception $e) {
            $this->view('error/500', ['message' => $e->getMessage()]);
        }
    }
}
