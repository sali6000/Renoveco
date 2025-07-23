<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\Controller;

class --replaceController extends Controller
{
    public function index()
    {
        $this->view('--replaceView', [
            'title' => 'Vue --replaceView',
            'message' => 'Contenu de la page --replaceView'
        ]);
    }
}
