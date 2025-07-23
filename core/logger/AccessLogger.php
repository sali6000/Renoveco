<?php

namespace App\Core\Logger;

class AccessLogger
{
    /**
     * Get the path of the log file for the current day.
     */
    private static function getLogFilePath(): string
    {
        return __DIR__ . '/../../storage/logs/' . date('Y-m-d') . '-access.log';
    }

    /**
     * Log a message to the daily access log file.
     */
    public static function log(string $message): void
    {
        // Ensure the log message is not empty
        if (empty($message)) {
            return;
        }

        // Format the log message with a timestamp
        $logFile = self::getLogFilePath();
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[$timestamp] $message" . PHP_EOL;

        // Ensure the log directory exists
        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Append the log line to the file
        file_put_contents($logFile, $logLine, FILE_APPEND);
    }
}
