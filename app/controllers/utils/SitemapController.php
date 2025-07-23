<?php

// Permet au SEO d'indexer le site

namespace App\Controllers\Utils;

use Core\Controller;

class SitemapController extends Controller
{
    public function index()
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = [
            [
                'loc' => BASE_URL,
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ]
        ];
        echo $this->generateSitemap($urls);
    }

    private function generateSitemap($urls)
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $sitemap .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $sitemap .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $url['priority'] . '</priority>';
            $sitemap .= '</url>';
        }
        $sitemap .= '</urlset>';
        return $sitemap;
    }
}
