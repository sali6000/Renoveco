<?php

namespace Core\Database;

/**
 * Helpers pour générer des fragments SQL réutilisables (JSON aggregation etc.).
 */
class SqlHelpers
{
    /**
     * Génère automatiquement une sous-requête JSON agrégée avec support
     * complet des JOINs et des colonnes multi-tables.
     *
     * Exemple produit :
     *
     *   COALESCE(
     *       (
     *        SELECT JSON_ARRAYAGG(
     *            JSON_OBJECT('id', c.id, 'name', c.name)
     *        )
     *        FROM category_product cp
     *        LEFT JOIN categories c ON c.id = cp.category_id
     *        WHERE cp.product_id = p.id
     *       ),
     *       JSON_ARRAY()
     *   ) AS categories
     *
     * @param string $fromTable  ex: 'category_product cp'
     * @param array  $joins      ex: ['LEFT JOIN categories c ON c.id = cp.category_id']
     * @param array  $columns    ex: ['c.id', 'c.name']
     * @param string $where      ex: 'cp.product_id'
     * @param string $equal      ex: 'p.id'
     * @param string $alias      ex: 'categories'
     */
    public static function jsonArrayAggreg(
        string $from,
        array $select,
        string $where,
        string $equal,
        string $alias,
        ?array $joins = []
    ): string {

        // =====================================================
        // 1. JSON_OBJECT('id', c.id, 'name', c.name)
        // =====================================================
        $jsonParts = [];
        foreach ($select as $column) {

            // On détecte la clé automatiquement : c.id → id
            $temp = explode('.', $column);
            $key = $temp[count($temp) - 1];

            $jsonParts[] = "'$key', $column";
        }

        $jsonObject = "JSON_OBJECT(" . implode(', ', $jsonParts) . ")";

        // =====================================================
        // 2. FROM + JOINS
        // =====================================================
        $joinSQL = '';
        if (!empty($joins)) {
            $joinSQL = ' ' . implode(' ', $joins);
        }

        // =====================================================
        // 3. Construction finale
        // =====================================================
        return "
        COALESCE(
            (
                SELECT JSON_ARRAYAGG($jsonObject)
                FROM $from
                $joinSQL
                WHERE $where = $equal
            ),
            JSON_ARRAY()
        ) AS $alias
    ";
    }
}
