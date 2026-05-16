<?php

namespace Src\Modules\Stock\Controllers;

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
