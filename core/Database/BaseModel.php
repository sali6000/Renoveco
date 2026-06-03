<?php

namespace Core\Database;

use DateTime;
use Src\Database\SchemaMysql;

abstract class BaseModel
{
    // ==========================================================
    // STRING
    // ==========================================================

    protected static function getString(array $row, string $const): string
    {
        return $row[SchemaMysql::fieldProperty($const)];
    }

    protected static function getStringOrNull(array $row, string $const): ?string
    {
        return $row[SchemaMysql::fieldProperty($const)] ?? null;
    }

    protected static function getStringOrEmpty(array $row, string $const): string
    {
        return $row[SchemaMysql::fieldProperty($const)] ?? '';
    }

    // ==========================================================
    // INT
    // ==========================================================

    protected static function getInt(array $row, string $const): int
    {
        return (int) $row[SchemaMysql::fieldProperty($const)];
    }

    protected static function getIntOrNull(array $row, string $const): ?int
    {
        $key = SchemaMysql::fieldProperty($const);
        return isset($row[$key]) ? (int) $row[$key] : null;
    }

    protected static function getIntOrZero(array $row, string $const): int
    {
        return (int) ($row[SchemaMysql::fieldProperty($const)] ?? 0);
    }

    // ==========================================================
    // FLOAT
    // ==========================================================

    protected static function getFloat(array $row, string $const): float
    {
        return (float) $row[SchemaMysql::fieldProperty($const)];
    }

    protected static function getFloatOrNull(array $row, string $const): ?float
    {
        $key = SchemaMysql::fieldProperty($const);
        return isset($row[$key]) ? (float) $row[$key] : null;
    }

    protected static function getFloatOrZero(array $row, string $const): float
    {
        return (float) ($row[SchemaMysql::fieldProperty($const)] ?? 0.0);
    }

    // ==========================================================
    // BOOL
    // ==========================================================

    protected static function getBoolOrFalse(array $row, string $const): bool
    {
        return (bool) ($row[SchemaMysql::fieldProperty($const)] ?? false);
    }

    protected static function getBoolOrTrue(array $row, string $const): bool
    {
        return (bool) ($row[SchemaMysql::fieldProperty($const)] ?? true);
    }

    // ==========================================================
    // DATETIME
    // ==========================================================

    protected static function getDateOrNull(array $row, string $const): ?DateTime
    {
        return self::toDateTime($row[SchemaMysql::fieldProperty($const)] ?? null);
    }

    // ==========================================================
    // JSON
    // ==========================================================

    protected static function getJsonOrEmpty(array $row, string $const): array
    {
        $key = SchemaMysql::fieldProperty($const);
        return !empty($row[$key])
            ? json_decode($row[$key], true)
            : [];
    }

    protected static function getJsonOrNull(array $row, string $const): ?array
    {
        $key = SchemaMysql::fieldProperty($const);
        return !empty($row[$key])
            ? json_decode($row[$key], true)
            : null;
    }

    // ==========================================================
    // RELATIONS (JSON décodé puis mappé vers des entités)
    // ==========================================================

    /**
     * Mappe un tableau JSON décodé vers un tableau d'entités
     * 
     * Exemple :
     * self::getMappedOrEmpty($row, 'images', [ProductImage::class, 'fromArray'])
     */
    protected static function getMappedOrEmpty(array $row, string $key, callable $mapper): array
    {
        return !empty($row[$key])
            ? array_map($mapper, $row[$key])
            : [];
    }

    /**
     * Mappe un tableau JSON décodé vers une entitée
     */
    protected static function getMappedOrNull(array $row, string $key, callable $mapper): ?object
    {
        return !empty($row[$key])
            ? $mapper($row[$key])
            : null;
    }

    // ==========================================================
    // HELPERS INTERNES
    // ==========================================================

    protected static function toDateTime(?string $value): ?DateTime
    {
        if ($value === null || $value === '') {
            return null;
        }
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        return $dt instanceof DateTime ? $dt : null;
    }
}
