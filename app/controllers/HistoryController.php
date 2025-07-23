<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;
use App\Services\HistoryService;

class HistoryController extends Controller
{
    private $historyService;

    public function __construct(HistoryService $historyService)
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct();
        $this->historyService = $historyService;
    }

    /**
     * Affiche l'historique de l'entreprise
     */
    public function index()
    {
        try {
            $datas = $this->historyService->getAllHistory();

            $this->set('datas', $datas);

            $this->view('history', $this->data);
        } catch (\Exception $e) {
            $this->view('error/500', ['message' => $e->getMessage()]);
        }
    }
}
