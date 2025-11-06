<?php

namespace Core\Database;

class AggregateTransformer
{
    // Conversion GROUP_CONCAT / subquery → array 
    /**
     * Transforme une chaîne issue de GROUP_CONCAT en tableau associatif
     *
     * @param string|null $groupConcatString Valeur SQL GROUP_CONCAT
     * @param string $rowSeparator Séparateur entre chaque élément (ex: '|')
     * @param string $columnSeparator Séparateur entre les colonnes (ex: ':')
     * @param array $keys Clés pour associer les colonnes (ex: ['id','slug','name'])
     *
     * @return array Tableau associatif
     */
    public function groupConcatToArray(?string $groupConcatString, array $keys = [], string $columnSeparator = ':', string $rowSeparator = '|'): array
    {
        $result = [];

        // 🧹 Nettoyage et vérification -> si groupConcatString est null, alors devient ''.
        $groupConcatString = trim($groupConcatString);

        if (empty($groupConcatString)) {
            return $result;
        }

        foreach (explode($rowSeparator, $groupConcatString) as $rowString) {

            $rowString = trim($rowString);
            if ($rowString === '') {
                continue; // ignore les lignes vides
            }

            $values = explode($columnSeparator, $rowString);

            $item = [];
            foreach ($keys as $index => $key) {
                $item[$key] = $values[$index] ?? null;
            }

            $result[] = $item;
        }

        return $result;
    }


    /**
     * Transforme une valeur unique issue d'une subquery en tableau homogène
     *
     * @param string|null $value Valeur issue de la subquery (ex: main_image)
     * @param array $keys Clés pour associer les valeurs (ex: ['file_path','alt_text'])
     *
     * @return array Tableau homogène pour hydrater les objets
     */
    public function subqueryToArray(?string $value, array $keys = []): array
    {
        if (empty($value)) {
            return [];
        }

        // Si aucune clé fournie, on retourne simplement la valeur brute
        if (empty($keys)) {
            return [$value];
        }

        // Si plusieurs clés, on peut supposer que $value est séparé par ":" (optionnel)
        $values = explode(':', $value);

        $item = [];
        foreach ($keys as $index => $key) {
            $item[$key] = $values[$index] ?? null;
        }

        return [$item];
    }
}
