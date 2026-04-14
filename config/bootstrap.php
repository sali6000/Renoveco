<?php
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Chargement de l'autoloader (Composer)
require_once BASE_PATH . '/vendor/autoload.php';

// Chargement du kernel et des dépendances
use Core\AppKernel;
use Core\Container;

// Chargement des dépendances
$container = new Container();

// Chargement des dépendances personnalisées
require BASE_PATH . '/config/services.php';

// Chargement de la route
return new AppKernel($container);
