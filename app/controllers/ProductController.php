<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use App\Core\Controller;
use App\Services\ProductService;

class ProductController extends Controller
{
  private $productService;

  public function __construct(ProductService $productService)
  {
    // Appeler explicitement le constructeur de la classe parente
    parent::__construct();
    $this->productService = $productService;
  }

  /**
   * Affiche les détails d'un produit spécifique
   */
  public function detail($id)
  {
    try {
      $product = $this->productService->getProductById($id);
      $previews = $this->productService->getImagesByProductIdExceptItself($id);

      $this->set('model', $product);
      $this->set('scheme', '');
      $this->set('previews', $previews);
      $this->set('base_url_assets_img_materials', BASE_URL_PUBLIC_ASSETS_IMG_MATERIALS);

      $this->view('product/detail', $this->data);
    } catch (\Exception $e) {
      $this->view('error/500', ['message' => $e->getMessage()]);
    }
  }

  /**
   * Affiche la liste de tous les produits
   */
  public function list()
  {
    try {
      $products = $this->productService->getAllProducts();
      $files_storefronts = $this->productService->countProductsInCategory('storefronts');
      $files_windows = $this->productService->countProductsInCategory('windows');
      $files_slidings = $this->productService->countProductsInCategory('slidings');

      $this->set('models', $products);
      $this->set('files_storefronts', $files_storefronts);
      $this->set('files_windows', $files_windows);
      $this->set('files_slidings', $files_slidings);
      $this->set('base_url_assets_img_materials', BASE_URL_PUBLIC_ASSETS_IMG_MATERIALS);

      $this->view('product/list', $this->data);
    } catch (\Exception $e) {
      $this->view('error/500', ['message' => $e->getMessage()]);
    }
  }
}
