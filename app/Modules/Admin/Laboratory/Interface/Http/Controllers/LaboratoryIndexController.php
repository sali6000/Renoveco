<?php

namespace App\Modules\Admin\Laboratory\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/admin/laboratory')]
class LaboratoryIndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct('Admin/Laboratory');
    }

    #[Route('', methods: ['GET'])]
    public function index(): void
    {
        $this->render();
    }
}
