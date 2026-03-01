<?php
// Core/Support/SecurityHelper

namespace Core\Support;

use App\Exception\ValidationException;

final class SecurityHelper
{
    public static function sanitizeString(
        mixed $string,
        string $fieldName,
        int $minLength = 1,
        int $maxLength = 50,
        bool $canBeEmpty = false,
        bool $canBeNull = false,
        bool $deleteStartAndEndSpaces = true,
        bool $stripTags = true,
        bool $escapeHtml = false,
        ?string $pattern = null,
        string $encoding = 'UTF-8'
    ) {
        // --- 1. NULL ou vide strict ----------------------------------------------
        if ($string === null || $string === '') {
            if ($string === null && $canBeNull) {
                return null;
            }

            if ($string === '' && $canBeEmpty) {
                return '';
            }

            throw new ValidationException("La valeur de " . $fieldName . " est vide ou nulle, ce qui n'est pas autorisé.", "sanitizeString");
        }

        // --- 2. Convertir en string -----------------------------------------------
        if (!is_string($string)) {
            throw new ValidationException("La valeur de " . $fieldName . " doit être une chaîne de caractères.", "sanitizeString");
        }

        // --- 3. Trim ---------------------------------------------------------------
        if ($deleteStartAndEndSpaces) {
            $string = trim($string);
        }

        // --- 4. Après trim, tester vide à nouveau ---------------------------------
        if ($string === '') {
            if ($canBeEmpty) {
                return '';
            }
            throw new ValidationException("La chaîne de " . $fieldName . " ne peut pas être vide.", "sanitize");
        }

        // --- 5. Supprimer balises HTML --------------------------------------------
        if ($stripTags) {
            $string = strip_tags($string);
        }

        // --- 6. Convertir caractères spéciaux (XSS) --------------------------------
        if ($escapeHtml) {
            $string = htmlspecialchars(
                $string,
                ENT_QUOTES | ENT_HTML5,
                $encoding
            );
        }

        // --- 7. Nettoyage : caractères de contrôle ---------------------------------
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
        $string = preg_replace('/\p{C}+/u', '', $string);

        // --- 8. Vérification longueur ----------------------------------------------
        $len = mb_strlen($string, $encoding);

        if ($len < $minLength) {
            throw new ValidationException("Longueur minimum de " . $fieldName . " non respectée ({$minLength}).", "sanitize");
        }

        if ($len > $maxLength) {
            throw new ValidationException("Longueur maximum de " . $fieldName . " dépassée ({$maxLength}).", "sanitize");
        }

        // --- 9. Regex pattern ------------------------------------------------------
        if ($pattern !== null && !preg_match($pattern, $string)) {
            throw new ValidationException("Le format de " . $fieldName . " est invalide.", "sanitize");
        }

        return $string;
    }

    // Ex: $parentId = SecurityHelper::sanitizeInt($_POST['parent_id'] ?? null, 1, 999, null);
    // null si non fourni, sinon int >=1

    // Autre ex: $quantity = SecurityHelper::sanitizeInt($_POST['quantity'] ?? null, 0, 1000, 0);
    // renvoie 0 si non fourni
    public static function sanitizeInt(mixed $value, string $fieldName, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX, bool $canBeNull = false): ?int
    {
        if ($value === null || $value === '') {
            if (!$canBeNull) {
                throw new ValidationException("La valeur attendue (int) de  " . $fieldName . " doit pas être NULL ni vide", "sanitize");
            }
            return null;
        }

        $result = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);

        if ($result === false) {
            throw new ValidationException("La valeur attendue (int) de " . $fieldName . " ne respecte pas le rang ou n'est pas un INT", "sanitize");
        }

        return $result; // valide, y compris 0
    }


    public static function sanitizeBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }
}
