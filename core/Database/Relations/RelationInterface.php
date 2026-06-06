<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;

/**
 * Interface pour gérer les relations entre entités.
 * Une relation est un ensemble de règles pour charger et hydrater des données liées.
 */
interface RelationInterface
{
    /**
     * Hydrate une collection de lignes brutes (issues d'un JOIN) en entités avec relations.
     *
     * @param array $rows Lignes brutes issues de la requête SQL
     * @return array Entités hydratées avec les relations chargées
     */
    public function hydrate(array $rows): array;

    /**
     * Retourne la clé de la relation (ex: 'roles', 'permissions').
     */
    public function getKey(): string;

    /**
     * Retourne les colonnes à sélectionner pour cette relation.
     * @return string[]
     */
    public function getColumns(): array;
    public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface; // chaque implémentation fait son propre join

}
