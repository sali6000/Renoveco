<?php
// Core/Logger/AccessLogger.php

namespace Core\Logger;

use Config\AppConfig;

/**
 * Logger statique avec séparation canal / niveau.
 *
 * Canal  → détermine le fichier de destination (kernel, routing, app...)
 * Niveau → qualifie la sévérité du message (error, warning, debug...)
 *
 * Usage :
 *   AccessLogger::log($message, AccessLogger::LEVEL_ERROR, AccessLogger::CHANNEL_KERNEL);
 *   AccessLogger::channel(AccessLogger::CHANNEL_ROUTING)->log($message, AccessLogger::LEVEL_WARNING);
 */
final class AccessLogger
{
    // -------------------------------------------------------------------------
    // Canaux — déterminent le fichier de log
    // -------------------------------------------------------------------------
    public const CHANNEL_KERNEL   = 'kernel';   // Erreurs système non catchées
    public const CHANNEL_ROUTING  = 'routing';  // 403, 404, erreurs de routing
    public const CHANNEL_SECURITY = 'security'; // CSRF, tentatives suspectes
    public const CHANNEL_APP      = 'app';      // Logs métier généraux
    public const CHANNEL_DATABASE = 'database'; // Erreurs SQL, queries
    public const CHANNEL_PERF     = 'perf';     // Performances, profiling
    public const CHANNEL_DEBUG     = 'debug';   // Debug


    // -------------------------------------------------------------------------
    // Niveaux — qualifient la sévérité
    // -------------------------------------------------------------------------
    public const LEVEL_ERROR   = 'error';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_INFO    = 'info';
    public const LEVEL_SUCCESS = 'success';
    public const LEVEL_DEBUG   = 'debug';

    // -------------------------------------------------------------------------
    // Icônes lisibles dans les fichiers de log
    // -------------------------------------------------------------------------
    private const ICONS = [
        self::LEVEL_ERROR   => '❌',
        self::LEVEL_WARNING => '⚠️',
        self::LEVEL_INFO    => 'ℹ️',
        self::LEVEL_SUCCESS => '✅',
        self::LEVEL_DEBUG   => '🔬',
    ];

    private const VALID_CHANNELS = [
        self::CHANNEL_KERNEL,
        self::CHANNEL_ROUTING,
        self::CHANNEL_SECURITY,
        self::CHANNEL_APP,
        self::CHANNEL_DATABASE,
        self::CHANNEL_PERF,
        self::CHANNEL_DEBUG
    ];

    private const VALID_LEVELS = [
        self::LEVEL_ERROR,
        self::LEVEL_WARNING,
        self::LEVEL_INFO,
        self::LEVEL_SUCCESS,
        self::LEVEL_DEBUG,
    ];

    // -------------------------------------------------------------------------
    // API fluide : AccessLogger::channel('kernel')->log(...)
    // -------------------------------------------------------------------------
    private string $boundChannel;

    private function __construct(string $channel)
    {
        $this->boundChannel = $channel;
    }

    public static function channel(string $channel): self
    {
        return new self(self::resolveChannel($channel));
    }

    public function log(string|\Throwable $message, string $level = self::LEVEL_INFO): void
    {
        self::write($message, $level, $this->boundChannel);
    }

    // -------------------------------------------------------------------------
    // API statique directe : AccessLogger::log($message, LEVEL, CHANNEL)
    // -------------------------------------------------------------------------
    public static function logTo(
        string|\Throwable $message,
        string $level   = self::LEVEL_INFO,
        string $channel = self::CHANNEL_APP,
        bool $includeHttpContext = true
    ): void {
        self::write($message, $level, $channel, $includeHttpContext);
    }

    // -------------------------------------------------------------------------
    // Écriture effective
    // -------------------------------------------------------------------------
    private static function write(
        string|\Throwable $message,
        string $level,
        string $channel,
        bool $includeHttpContext = true
    ): void {
        $level   = self::resolveLevel($level);
        $channel = self::resolveChannel($channel);

        $body = $message instanceof \Throwable
            ? (string) $message          // inclut message + stack trace
            : trim($message);

        if ($body === '') return;

        $icon      = self::ICONS[$level] ?? '';
        $timestamp = date('Y-m-d H:i:s');
        $requestId = defined('REQUEST_ID') ? REQUEST_ID : 'no-rid';

        $http = '';
        if ($includeHttpContext && isset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])) {
            $http = sprintf(
                '[%s][%s %s]',
                $_SERVER['REMOTE_ADDR'] ?? 'n/a',
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI']
            );
        }

        $line = sprintf(
            "[%s][%s][%s][RID:%s]%s %s %s",
            $timestamp,
            strtoupper($channel),
            strtoupper($level),
            $requestId,
            $http !== '' ? " $http" : '',
            $icon,
            $body
        ) . PHP_EOL;

        $logDir = AppConfig::getConst('SHARED_PATH') . 'storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Un fichier par canal et par jour : 2025-01-23-kernel.log
        $logFile = sprintf('%s/%s-%s.log', $logDir, date('Y-m-d'), $channel);

        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    // -------------------------------------------------------------------------
    // Résolution / fallback
    // -------------------------------------------------------------------------
    private static function resolveLevel(string $level): string
    {
        $level = strtolower($level);
        return in_array($level, self::VALID_LEVELS, strict: true)
            ? $level
            : self::LEVEL_INFO;
    }

    private static function resolveChannel(string $channel): string
    {
        $channel = strtolower($channel);
        return in_array($channel, self::VALID_CHANNELS, strict: true)
            ? $channel
            : self::CHANNEL_APP;
    }
}
