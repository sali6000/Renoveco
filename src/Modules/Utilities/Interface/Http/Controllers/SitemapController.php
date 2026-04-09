<?php

// Permet au SEO d'indexer le site
// ✅ Ce commentaire explique la finalité globale de ce contrôleur : générer un sitemap pour les moteurs de recherche.
// 🔑 Indispensable pour comprendre l'intention du fichier quand on le lit rapidement.

/**
 * C’est un fichier XML qui liste toutes les pages importantes de ton site.
 * Il permet aux moteurs de recherche (Google, Bing…) de trouver et indexer toutes tes pages rapidement, même celles qui sont difficiles à atteindre via les liens classiques.
 * Tu peux y inclure :
 * URL de la page
 * Date de dernière modification (<lastmod>)
 * Priorité de la page (<priority>)
 * Fréquence de mise à jour (<changefreq>)
 * ... et d’autres métadonnées utiles pour le SEO.
 */

namespace Src\Modules\Utilities\Interface\Http\Controllers;
// ✅ Déclare l’espace de nom (namespace) de la classe.
// 🔑 Utile pour l’organisation et l’autoloading PSR-4. Nécessaire si tu veux un code propre et modulable.

use Config\AppConfig;
use Core\BaseController;
use Core\Routing\Attribute\Route;

// ✅ "use" permet d’importer d’autres classes (ici ton contrôleur de base et ta config d’app).
// 🔑 Obligatoire si tu veux les utiliser sans devoir écrire leur namespace complet.

#[Route('/sitemap.xml')]
class SitemapController extends BaseController
{
    #[Route('', methods: ['GET'])]
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $links = require AppConfig::getConst('ROOT_PATH_CONFIG') . 'sitemap.php';
        $baseUrl = substr(AppConfig::getConst('URL_PATH'), 0, -1);
        $sitemap = [];

        foreach ($links as $link) {
            $sitemap[] = [
                'loc' => $baseUrl . $link['loc'],
                'lastmod' => date('Y-m-d'),
                'priority' => $link['priority'],
                'lastmod' => $link['lastmod'],
                'changefreq' => $link['changefreq']
            ];
        }

        echo $this->generateSitemap($sitemap);
        // ✅ Appelle une méthode privée pour générer le XML complet et l'affiche.
        // 🔑 Séparation logique du code (lisibilité et réutilisabilité).
        exit; // ⚠️ Important pour stopper tout flux supplémentaire
    }

    private function generateSitemap(array $urls): string
    // ✅ Méthode qui construit le fichier XML en se basant sur le tableau d’URLs.
    // 🔑 Bonne pratique : code encapsulé, facile à tester/modifier sans toucher à "index()".
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        // ✅ En-tête XML obligatoire pour un document valide.

        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        // ✅ Balise racine <urlset> avec namespace officiel du protocole Sitemap.
        // 🔑 Indispensable pour que les moteurs comprennent ton XML.

        foreach ($urls as $url) {
            // ✅ Boucle sur chaque URL à inclure dans le sitemap.
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            // ✅ htmlspecialchars protège les caractères spéciaux (&, <, >) pour XML valide.
            $sitemap .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $sitemap .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $url['priority'] . '</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';
        // ✅ Ferme la balise racine.
        return $sitemap;
        // ✅ Retourne le XML final sous forme de string.
    }
}
