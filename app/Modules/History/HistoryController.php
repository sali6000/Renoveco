<?php

namespace App\Modules\About;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Modules\About\HistoryService;
use App\Exception\ServiceException;
use Core\Controller;
use Core\Logger\AccessLogger;

class HistoryController extends Controller
{
    public function __construct(private HistoryService $historyService)
    {
        parent::__construct();
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
        } catch (ServiceException $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ HistoryController::index → ServiceException : " . $e, AccessLogger::LEVEL_ERROR);

            $message = ($_ENV['APP_ENV'] === 'dev')
                ? $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>'
                : "Une erreur est survenue (Code : $errorId)";

            $this->view('error/500', ['message' => $message]);
        } catch (\Throwable $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ HistoryController::index → Exception système : " . $e, AccessLogger::LEVEL_ERROR);

            $message = ($_ENV['APP_ENV'] === 'dev')
                ? $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>'
                : "Une erreur technique est survenue (Code : $errorId)";

            $this->view('error/500', ['message' => $message]);
        }
    }
}
