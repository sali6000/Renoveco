<?php

// Permet au SEO d'indexer le site
// ✅ Ce commentaire explique la finalité globale de ce contrôleur : générer un sitemap pour les moteurs de recherche.
// 🔑 Indispensable pour comprendre l'intention du fichier quand on le lit rapidement.

namespace App\Controllers\Utilities;
// ✅ Déclare l’espace de nom (namespace) de la classe.
// 🔑 Utile pour l’organisation et l’autoloading PSR-4. Nécessaire si tu veux un code propre et modulable.

use Core\Controller;
use Config\AppConfig;
// ✅ "use" permet d’importer d’autres classes (ici ton contrôleur de base et ta config d’app).
// 🔑 Obligatoire si tu veux les utiliser sans devoir écrire leur namespace complet.

class SitemapController extends Controller
// ✅ Déclaration de la classe "SitemapController" qui hérite de "Controller".
// 🔑 Héritage important : permet de bénéficier de fonctionnalités communes (ex: rendering, redirection).

{
    public function index()
    // ✅ Méthode publique appelée quand l’URL correspond à "/sitemap" (ou ce que ton routeur décide).
    // 🔑 Point d’entrée de la génération du sitemap.
    {
        header('Content-Type: application/xml; charset=utf-8');
        // ✅ Définit le type de contenu de la réponse HTTP sur "application/xml".
        // 🔑 Essentiel : les navigateurs et moteurs de recherche sauront que c’est un fichier XML.

        $urls = [
            [
                'loc' => AppConfig::getPath('APP_PATH_URL'),
                // ✅ Génère l’URL principale de ton site depuis ta configuration.
                // 🔑 Permet d’éviter les URL en dur, rend le code portable.

                'lastmod' => date('Y-m-d'),
                // ✅ Date de dernière modification (format ISO 8601 attendu par Google).
                // 🔑 Utile pour le SEO : indique si la page est fraîche.

                'changefreq' => 'daily',
                // ✅ Donne un indice aux moteurs sur la fréquence de mise à jour.
                // 🔑 Pas obligatoire, mais améliore le référencement.

                'priority' => '1.0'
                // ✅ Priorité relative de l’URL (entre 0.0 et 1.0).
                // 🔑 Permet aux moteurs de prioriser cette page dans l’indexation.
            ]
        ];
        echo $this->generateSitemap($urls);
        // ✅ Appelle une méthode privée pour générer le XML complet et l'affiche.
        // 🔑 Séparation logique du code (lisibilité et réutilisabilité).
    }

    private function generateSitemap($urls)
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
