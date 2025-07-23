<?php
// 1. Sécurisation de l'accès direct au fichier
define('SECURE_CHECK', true);

// ----- PROCESSUS D'INITIALISATION DE L'APPLICATION -----
// 3. Instantiacion du Router pour gérer les actions et parametres liés à la route (ex: http://...MonSite\product\detail\1)
use App\Core\Router;
use Dotenv\Dotenv;


// 5. Instantiation des variables d'environnement et des fichiers requis (via autoload composer : PSR-4)

require_once realPath(__DIR__ . '/../config/routes.php'); // Charge les variables d'environnement définis (BASE_PATH, BASE_URL, etc...)
require_once BASE_PATH_VENDOR_AUTOLOAD; // Charge et inclut ("require_once") automatiquement les fichiers solicités dans le projet (sur base du composer.json)

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();
// ----- PROCESSUS DE TRAITEMENT DE LA REQUETE -----
// 6. Récupération de l'URI 
// 7. Transmission de l'URI au routeur
$uri = $_GET['index'] ?? $_SERVER['REQUEST_URI'];
$router = new Router($uri);

// 9. Exezcution de la route (Le routeur va instancier le controller approprié et appeler la méthode correspondante)
$router->route();
