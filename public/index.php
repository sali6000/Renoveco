<?php
define('BASE_PATH', realpath(__DIR__ . '/..'));

try {
    $kernel = require BASE_PATH . '/config/bootstrap.php';

    if (!is_object($kernel) || !method_exists($kernel, 'handle')) {
        throw new \RuntimeException('Bootstrap did not return a valid kernel instance.');
    }
    $result = $kernel->handle();
} catch (\Throwable $e) {
    error_log('Critical error in public/index.php: ' . $e);
    $env = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? null);
    if ($env === 'dev') {
        echo "<h1>Erreur critique (index.php)</h1><pre>" . htmlspecialchars((string)$e) . "</pre>";
    } else {
        echo "Une erreur technique est survenue. Veuillez contacter l'administrateur." . htmlspecialchars((string)$e);
    }
}
