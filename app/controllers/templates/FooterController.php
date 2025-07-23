<?php

namespace App\Controllers\Templates;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;

class FooterController extends Controller
{
    public function index(): array
    {
        // Ici, vous pouvez ajouter la logique pour récupérer les données nécessaires au footer
        // Par exemple, des liens, des informations de contact, etc.

        return [
            'footerText' => '© 2023 MonSite. Tous droits réservés.',
            'socialLinks' => [
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com'
            ]
        ];
    }
}
