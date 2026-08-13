<?php

declare(strict_types=1);

namespace Config;

use Dotenv\Dotenv;

final class EnvLoader
{
    private string $environment;

    public function __construct()
    {
        // Charger ./.env (pour Github)
        Dotenv::createImmutable(AppConfig::getConst('SHARED_PATH'))->safeLoad();

        // Récupérer l'environnement APP_ENV définit dans .env  
        $this->environment = $this->detectEnvironment();
    }

    private function detectEnvironment(): string
    {
        // Récupérer l'environnement APP_ENV définit dans .env  
        return AppConfig::getEnv('APP_ENV');
    }

    public function load(): void
    {
        // Chargement des variables d'environnement
        $this->loadEnvs();
    }

    private function loadEnvs()
    {
        $envsRepository = AppConfig::getConst('ROOT_PATH_STORAGE_SECURE');
        $tempRepository = AppConfig::getConst('ROOT_PATH_TMP');
        $scopes = [$this->environment]; // prod ou dev

        foreach ($scopes as $scope) {
            $file = $envsRepository . ".env.{$scope}.enc";
            $plainFile = $envsRepository . ".env.{$scope}";
            $dest = $tempRepository . ".env.{$scope}";
            $key = KeysManager::getKeyPath($scope);
            if (file_exists($file) && $key) {
                $this->decryptFile($file, $key, $dest);
                $this->loadEnvTempByScope($tempRepository, $scope);
                $this->deleteFile($dest);
            } elseif (file_exists($plainFile) && AppConfig::getEnv('APP_ENV') === 'dev') {
                Dotenv::createUnsafeMutable($envsRepository, [".env.{$scope}"])->safeLoad();
            }
        }
    }

    private function decryptFile(string $file, string $key, string $destination): void
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException("L'extension OpenSSL PHP est requise pour déchiffrer les fichiers d'environnement.");
        }

        if (!is_file($file) || !is_file($key)) {
            throw new \RuntimeException("Fichier chiffré ou clé introuvable: $file / $key");
        }

        $ciphertext = file_get_contents($file);
        if ($ciphertext === false) {
            throw new \RuntimeException("Lecture du fichier chiffré impossible: $file");
        }

        if (!str_starts_with($ciphertext, 'Salted__')) {
            throw new \RuntimeException("Format de fichier chiffré non supporté: $file");
        }

        $rawPassword = file_get_contents($key);
        if ($rawPassword === false) {
            throw new \RuntimeException("Lecture de la clé de déchiffrement impossible: $key");
        }

        $passwordCandidates = [$rawPassword];
        $withoutTrailingNewline = preg_replace('/\r?\n\z/', '', $rawPassword);
        if ($withoutTrailingNewline !== null && $withoutTrailingNewline !== $rawPassword) {
            $passwordCandidates[] = $withoutTrailingNewline;
        }

        $withoutBom = preg_replace('/^\xEF\xBB\xBF/', '', $rawPassword);
        if ($withoutBom !== null && $withoutBom !== $rawPassword) {
            $passwordCandidates[] = $withoutBom;
        }

        $salt = substr($ciphertext, 8, 8);
        $encrypted = substr($ciphertext, 16);
        $plainText = null;

        foreach (array_unique($passwordCandidates) as $password) {
            $derivedKey = openssl_pbkdf2($password, $salt, 48, 10000, 'sha256');
            if ($derivedKey === false) {
                continue;
            }

            $keyBytes = substr($derivedKey, 0, 32);
            $iv = substr($derivedKey, 32, 16);
            $plainText = openssl_decrypt($encrypted, 'aes-256-cbc', $keyBytes, OPENSSL_RAW_DATA, $iv);

            if ($plainText !== false) {
                break;
            }
        }

        if ($plainText === false || $plainText === null) {
            throw new \RuntimeException('Échec du déchiffrement OpenSSL: ' . openssl_error_string());
        }

        $directory = dirname($destination);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException("Impossible de créer le dossier de destination: $directory");
        }

        if (file_put_contents($destination, $plainText) === false) {
            throw new \RuntimeException("Impossible d'écrire le fichier déchiffré: $destination");
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
}
