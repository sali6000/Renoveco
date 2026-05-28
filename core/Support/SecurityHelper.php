<?php

namespace Core\Support;

use Src\Exception\Http\ValidationException;

final class SecurityHelper
{
    // ==========================================================
    // SANITIZATION (transforme, nettoie, normalise)
    // ==========================================================

    public static function sanitizeString(
        mixed $string,
        bool $deleteStartAndEndSpaces = true,
        bool $stripTags = true,
        bool $escapeHtml = false,
        string $encoding = 'UTF-8'
    ): ?string {
        if ($string === null || $string === '') {
            return $string === null ? null : '';
        }

        if (!is_string($string)) {
            return null;
        }

        if ($deleteStartAndEndSpaces) {
            $string = trim($string);
        }

        if ($stripTags) {
            $string = strip_tags($string);
        }

        if ($escapeHtml) {
            $string = htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, $encoding);
        }

        // Caractères de contrôle
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
        $string = preg_replace('/\p{C}+/u', '', $string);

        return $string;
    }

    public static function sanitizeInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $result = filter_var($value, FILTER_VALIDATE_INT);
        return $result !== false ? $result : null;
    }

    public static function sanitizeBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public static function sanitizeEmail(mixed $email): ?string
    {
        if ($email === null || $email === '') {
            return null;
        }

        if (!is_string($email)) {
            return null;
        }

        return trim($email);
    }

    // ==========================================================
    // VALIDATION (vérifie les règles, lance exception si invalide)
    // ==========================================================

    public static function validateString(
        mixed $string,
        string $fieldName,
        int $minLength = 1,
        int $maxLength = 50,
        bool $canBeEmpty = false,
        bool $canBeNull = false,
        ?string $pattern = null,
        string $encoding = 'UTF-8'
    ): ?string {
        // --- 1. NULL ---
        if ($string === null) {
            if ($canBeNull) return null;
            throw new ValidationException("La valeur de {$fieldName} est nulle, ce qui n'est pas autorisé.", "validateString");
        }

        // --- 2. Vide ---
        if ($string === '') {
            if ($canBeEmpty) return '';
            throw new ValidationException("La valeur de {$fieldName} est vide, ce qui n'est pas autorisé.", "validateString");
        }

        // --- 3. Type ---
        if (!is_string($string)) {
            throw new ValidationException("La valeur de {$fieldName} doit être une chaîne de caractères.", "validateString");
        }

        // --- 4. Longueur ---
        $len = mb_strlen($string, $encoding);
        if ($len < $minLength) {
            throw new ValidationException("Longueur minimum de {$fieldName} non respectée ({$minLength}).", "validateString");
        }
        if ($len > $maxLength) {
            throw new ValidationException("Longueur maximum de {$fieldName} dépassée ({$maxLength}).", "validateString");
        }

        // --- 5. Pattern ---
        if ($pattern !== null && !preg_match($pattern, $string)) {
            throw new ValidationException("Le format de {$fieldName} est invalide.", "validateString");
        }

        return $string;
    }

    public static function validateInt(
        mixed $value,
        string $fieldName,
        int $min = PHP_INT_MIN,
        int $max = PHP_INT_MAX,
        bool $canBeNull = false
    ): ?int {
        if ($value === null) {
            if ($canBeNull) return null;
            throw new ValidationException("La valeur (int) de {$fieldName} ne peut pas être nulle.", "validateInt");
        }

        $result = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);

        if ($result === false) {
            throw new ValidationException("La valeur (int) de {$fieldName} ne respecte pas le rang ou n'est pas un entier.", "validateInt");
        }

        return $result;
    }

    public static function validateEmail(
        mixed $email,
        string $fieldName = 'Email',
        int $minLength = 5,
        int $maxLength = 254,
        bool $canBeNull = false
    ): ?string {
        // --- 1. NULL ---
        if ($email === null) {
            if ($canBeNull) return null;
            throw new ValidationException("L'adresse {$fieldName} est obligatoire.", "validateEmail");
        }

        // --- 2. Vide ---
        if ($email === '') {
            if ($canBeNull) return null;
            throw new ValidationException("L'adresse {$fieldName} est obligatoire.", "validateEmail");
        }

        // --- 3. Type ---
        if (!is_string($email)) {
            throw new ValidationException("L'adresse {$fieldName} doit être une chaîne de caractères.", "validateEmail");
        }

        // --- 4. Longueur ---
        $len = mb_strlen($email);
        if ($len < $minLength) {
            throw new ValidationException("L'adresse {$fieldName} est trop courte ({$minLength} caractères minimum).", "validateEmail");
        }
        if ($len > $maxLength) {
            throw new ValidationException("L'adresse {$fieldName} est trop longue ({$maxLength} caractères maximum).", "validateEmail");
        }

        // --- 5. Format RFC ---
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Le format de l'adresse {$fieldName} est invalide.", "validateEmail");
        }

        return $email;
    }
}
