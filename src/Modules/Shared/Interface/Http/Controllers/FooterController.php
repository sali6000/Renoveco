<?php

namespace Src\Modules\Shared\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;

class FooterController extends BaseController
{
    public function index(): array
    {
        // Ici, vous pouvez ajouter la logique pour récupérer les données nécessaires au footer
        // Par exemple, des liens, des informations de contact, etc.

        return [];
    }
}
