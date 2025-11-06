<?php

declare(strict_types=1);

namespace Config;

use Dotenv\Dotenv;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

final class EnvLoader
{
    private string $basePath;
    private string $environment;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        // Étape 1 : charger le .env (par défaut, non chiffré)
        Dotenv::createImmutable($this->basePath)->safeLoad();

        // Étape 2 : maintenant, on peut lire APP_ENV, etc...
        $this->environment = $this->detectEnvironment();
    }

    public function load(): void
    {
        // Étape 2a : déchiffrement automatique du .env.{env}.enc (ex: .env.prod.enc)
        $encryptedFile = "/var/www/.env.{$this->environment}.enc";
        $keyFilePath = KeysManager::getKeyPath($this->environment);
        $decryptedFile = "/var/www/temp/.env.{$this->environment}";

        if (file_exists($encryptedFile) && $keyFilePath) {
            $cmd = "openssl enc -aes-256-cbc -pbkdf2 -d -in \"$encryptedFile\" -out \"$decryptedFile\" -pass file:$keyFilePath";
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException("Échec du déchiffrement de $encryptedFile");
            }

            Dotenv::createUnsafeMutable('/var/www/temp', [".env.{$this->environment}"])->safeLoad();
            unlink($decryptedFile);
        }

        // Étape 2b : déchiffrement automatique du .env.local.enc SI la clé est là
        $localEncryptedFile = "/var/www/.env.local.enc";
        $localKeyPath = KeysManager::getKeyPath('local');
        $localDecrypted = "/var/www/temp/.env.local";

        if (file_exists($localEncryptedFile) && $localKeyPath) {
            $cmd = "openssl enc -aes-256-cbc -pbkdf2 -d -in \"$localEncryptedFile\" -out \"$localDecrypted\" -pass file:$localKeyPath";
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException("Échec du déchiffrement de $localEncryptedFile");
            }

            Dotenv::createUnsafeMutable('/var/www/temp', ['.env.local'])->safeLoad();
            unlink($localDecrypted);
        }
    }

    private function detectEnvironment(): string
    {
        return $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'local';
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
