<?php

namespace Src\Modules\Stock\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;

class StockController extends BaseController
{
    public function __construct()
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct('Stock');
    }

    public function index()
    {
        $this->render();
    }
}
