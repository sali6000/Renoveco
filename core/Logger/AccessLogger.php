<?php
// core/Logger/AccessLogger.php

namespace Core\Logger;

class AccessLogger
{
    public const LEVEL_ERROR   = 'error';
    public const LEVEL_SUCCESS = 'success';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_DEBUG   = 'debug';
    public const LEVEL_SECURITY = 'security';
    public const LEVEL_PERF    = 'perf';
    public const LEVEL_INFO    = 'info';

    /** 
     * Icônes par niveau
     */
    private static array $levelIcons = [
        self::LEVEL_ERROR   => "❌",
        self::LEVEL_SUCCESS => "✅",
        self::LEVEL_WARNING => "⚠️",
        self::LEVEL_DEBUG   => "🐞",
        self::LEVEL_SECURITY => "❌",
        self::LEVEL_PERF    => "🚀",
        self::LEVEL_INFO    => "ℹ️",
    ];

    /**
     * Enregistre un message dans le fichier de log correspondant.
     *
     * @param string $message Le message à logger.
     * @param string $level Le niveau du log : error, warning, debug, perf.
     * @param string|null $customPath Optionnel : dossier personnalisé pour les logs.
     * @param bool $includeHttpContext Ajoute les infos de contexte HTTP si vrai.
     */
    public static function log(string $message, string $level = self::LEVEL_DEBUG, ?string $customPath = null, bool $includeHttpContext = true): void
    {
        if (empty($message)) return;

        // Sécuriser le niveau
        $level = strtolower($level);
        if (!in_array($level, [
            self::LEVEL_ERROR,
            self::LEVEL_SUCCESS,
            self::LEVEL_WARNING,
            self::LEVEL_DEBUG,
            self::LEVEL_SECURITY,
            self::LEVEL_PERF,
            self::LEVEL_INFO
        ])) {
            $level = self::LEVEL_INFO;
        }

        // Ajouter l'icône correspondante
        $icon = self::$levelIcons[$level] ?? '';
        $message = "$icon $message";

        $timestamp = date('Y-m-d H:i:s');
        $requestId = defined('REQUEST_ID') ? REQUEST_ID : 'no-rid';

        // Ajouter les infos HTTP si disponibles et souhaitées
        if ($includeHttpContext && isset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])) {
            $context = sprintf(
                "[%s][%s][%s]",
                $_SERVER['REMOTE_ADDR'] ?? 'n/a',
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI']
            );
            $message = "$context $message"; // Multiligne
        }

        $formattedMessage = "[$timestamp][$level][RID:$requestId] $message" . PHP_EOL;

        $logDir = $customPath ?? __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . "-$level.log";
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }
}
