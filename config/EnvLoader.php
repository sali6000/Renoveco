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

        // Charger .env
        Dotenv::createImmutable($this->basePath)->safeLoad();

        // Récupérer l'environnement APP_ENV définit dans .env  
        $this->environment = $this->detectEnvironment();
    }

    public function load(): void
    {
        // Chargement des variables d'environnement
        $this->loadEnvs();
    }

    private function loadEnvs()
    {
        $envsRepository = AppConfig::getConst('LOCAL_PATH');
        $tempRepository = $envsRepository . '/tmp';
        $scopes = ['local', $this->environment]; // local, prod/local

        foreach ($scopes as $scope) {
            $file = $envsRepository . "/.env.{$scope}.enc";
            $dest = $tempRepository . "/.env.{$scope}";
            $key = KeysManager::getKeyPath($scope);
            if (file_exists($file) && $key) {
                $this->decryptFile($file, $key, $dest);
                $this->loadEnvTempByScope($tempRepository, $scope);
                $this->deleteFile($dest);
            }
        }
    }

    private function loadEnvTempByScope($tempRepository, $scope)
    {
        // Charger le fichier d'environnement
        Dotenv::createUnsafeMutable($tempRepository, ['.env.' . $scope])->safeLoad();
    }

    private function deleteFile($file)
    {
        // Supprime le fichier d'enfironnement
        unlink($file);
    }

    private function decryptFile($file, $key, $destination)
    {
        $cmd = "openssl enc -aes-256-cbc -pbkdf2 -d -in \"$file\" -out \"$destination\" -pass file:$key";
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("Échec du déchiffrement de $file");
        }
    }

    private function detectEnvironment(): string
    {
        return AppConfig::getEnv('APP_ENV');
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
