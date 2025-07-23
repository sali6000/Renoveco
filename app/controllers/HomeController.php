<?php
// app/controllers/HomeController.php
namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use App\Core\Controller;

class HomeController extends Controller
{
  public function __construct()
  {
    // Appeler explicitement le constructeur de la classe parente
    parent::__construct();
  }

  /**
   * Affiche la page d'accueil
   */
  public function index()
  {
    // Affichage des sections (devis/installation/catégories/....)
    $sections = [
      'image11.jpg',
      'image12.jpg',
      'image13.jpg'
    ];

    $this->set('files_public', $sections);
    $this->set('base_url_assets_webm', BASE_URL_PUBLIC_ASSETS_WEBM);
    $this->set('base_url_assets_gallery', BASE_URL_PUBLIC_ASSETS_IMG_GALLERY);

    $this->view('home/index', $this->data);
  }
}
