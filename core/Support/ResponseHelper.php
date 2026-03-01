<?php
// core/ResponseHelper.php

namespace Core\Support;

class ResponseHelper
{
    public static function success($message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error($message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message, 'data' => $data]);
    }
}
