<?php

// Chargement de l'autoloader (Composer)
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement du kernel et des dépendances
use Core\AppKernel;
use Core\Container;

// Chargement des dépendances
$container = new Container();

// Chargement des dépendances personnalisées
require __DIR__ . '/services.php';

// Chargement de la route
return new AppKernel($container);
