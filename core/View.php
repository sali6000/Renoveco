<?php
// core/View.php

namespace App\Core;

use App\Controllers\Templates\HeaderController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{

    /**
     * Instance de Twig, initialisée une seule fois
     */
    private static ?Environment $twig = null;

    /**
     * Initialise Twig une seule fois
     */
    private static function initTwig()
    {
        // Si Twig n'est pas encore initialisé, on le fait
        // Utilise FilesystemLoader pour charger les templates depuis le dossier des vues
        if (self::$twig === null) {
            $loader = new FilesystemLoader(BASE_PATH_APP_VIEWS);
            self::$twig = new Environment($loader, [
                'cache' => false, // Pour dev, sinon dossier cache
                'debug' => true,
                'auto_reload' => true,
            ]);
        }
    }

    /**
     * Rend une vue avec Twig
     */
    public static function render(string $template, array $data = [])
    {
        self::initTwig();

        // Fusionne les données du contrôleur avec celles du header pour les mettres à disposition dans le layout
        $mergedData = array_merge($data, self::getHeaderData());

        // Affiche les données dans le template Twig
        echo self::$twig->render($template . '.twig', $mergedData);
    }

    /**
     * Permet d'inclure un composant Twig depuis un autre endroit (ex: widgets)
     */
    public static function renderPartial(string $template, array $data = [])
    {
        self::initTwig();
        echo self::$twig->render($template . '.twig', $data);
    }

    /**
     * Récupère les données du HeaderController pour les inclure dans le rendu
     */
    private static function getHeaderData(): array
    {
        $headerController = ControllerFactory::create(HeaderController::class);
        return $headerController->index(); // retourne un array
    }
}
