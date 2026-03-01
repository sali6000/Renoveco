<?php

/**
 * phpstan-bootstrap.php
 *
 * Ce fichier sert uniquement **à PHPStan** lors de l’analyse statique.
 * 
 * Son rôle principal :
 * 1️⃣ Définir des constantes “dummy” ou des variables globales utilisées par le code,
 *    afin que PHPStan ne crie pas “constant not found” ou “undefined variable”.
 * 2️⃣ Fournir des types corrects pour PHPStan, ce qui permet une meilleure détection d’erreurs
 *    et des inférences plus précises (ex: savoir si une constante est un string, int, array…).
 * 
 * Important :
 * - Ces valeurs ne sont pas utilisées en production. Elles n’ont aucun effet sur l’application réelle.
 * - On peut mettre des valeurs vides ou factices (ex : '', 0, []) si ça suffit pour satisfaire PHPStan.
 * - Ne jamais mettre de logique métier réelle ici. C’est uniquement pour l’analyse statique.
 */
if (!defined('SECURE_CHECK')) {
    define('SECURE_CHECK', true);
}
if (!defined('REQUEST_START_TIME')) {
    define('REQUEST_START_TIME', 0.0);
}

if (!defined('LOCAL_PATH')) {
    define('LOCAL_PATH', 'donnée factice');
}

if (!defined('URL_PATH')) {
    define('URL_PATH', 'donnée factice');
}
