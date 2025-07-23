<?php

namespace App\Controllers\Templates;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;
use App\Services\ProductService;

class HeaderController extends Controller
{
    private $baseUri;
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->baseUri = $this->determineBaseUri();
        $this->productService = $productService;
    }

    public function index(): array
    {
        $searchbarDatas = $this->productService->getAllProducts();

        return [
            'baseUri' => $this->baseUri,
            'searchbarDatas' => $searchbarDatas,
            'base_url_assets_img_logos' => BASE_URL_PUBLIC_ASSETS_IMG_LOGOS,
            'base_url_assets_css' => BASE_URL_PUBLIC_ASSETS_CSS,
            'base_url_assets_css_templates' => BASE_URL_PUBLIC_ASSETS_CSS_TEMPLATES,
            'base_url_assets_js' => BASE_URL_PUBLIC_ASSETS_JS,
            'base_url_assets_img' => BASE_URL_PUBLIC_ASSETS_IMG
        ];
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    // Vérifie si l'hôte contient 'localhost' pour définir la base URI appropriée
    private function determineBaseUri()
    {
        return (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) ? "http://$_SERVER[HTTP_HOST]/" : BASE_URL;
    }
}
