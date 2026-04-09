<?php

namespace Src\Modules\Category\Domain\Service;

use Src\Database\SchemaMysql;
use Src\Exception\ServiceException;
use Src\Exception\UniqueConstraintException;
use Src\Exception\ValidationException;
use Src\Modules\Category\Domain\Entity\Category;
use Src\Modules\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Logger\AccessLogger;
use PDOException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $categoryRepo) {}

    public function createCategory(Category $category): Category
    {
        try {
            if (empty($category->name)) {
                throw new ValidationException("Le nom de la catégorie est obligatoire.", $category->name);
            }

            if (strlen($category->name) > 100) {
                throw new ValidationException("Le nom ne peut dépasser 100 caractères.", $category->name);
            }

            if (!preg_match('/^[a-z0-9-]+$/i', $category->slug)) {
                throw new ValidationException("Le slug contient des caractères invalides.", $category->slug);
            }
            return $this->categoryRepo->save($category); // ⚡ Sauvegarde via Repository
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Si duplication à cause d'une contrainte UNIQUE
                $message = $e->errorInfo[2]; // Ex: Duplicate entry ... for key 'users.email'

                // Si le message d'erreur contient "users.email" renvoyer une erreur sur "email"
                if (str_contains(
                    $message,
                    SchemaMysql::fieldTable(SchemaMysql::TABLE_CATEGORIES) . "." .
                        SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_SLUG)
                )) {
                    throw new UniqueConstraintException(SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_SLUG));
                }

                // Sinon logguer l'erreur et renvoyer une UniqueConstraintException générique
                $errorId = uniqid('cat_srvc_pdo_', true);
                AccessLogger::log("Contrainte UNIQUE inconnue (Code : $errorId) " . $message, AccessLogger::LEVEL_WARNING);
                throw new UniqueConstraintException("unknown");
            }
            throw $e;
        } catch (\Throwable $e) {
            $errorId = uniqid('cat_srvc_', true);
            AccessLogger::log("Erreur de service (Code : $errorId) " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Erreur de création d'une catégorie (Code : $errorId)", 0, $e, $errorId);
        }
    }

    public function getCategories(): array
    {
        return $this->categoryRepo->findAll();
    }

    /**
     * Retourne toutes les catégories sous forme d'arbre hiérarchique
     * @return Category[]
     *
     */
    public function getCategoriesTree(): array
    {
        // Retourne un array composé d'objets de type "Category"
        $categories = $this->categoryRepo->findAll();

        $byId = [];

        foreach ($categories as $category) {
            $byId[$category->id] = $category; // Ex: $byId[12] = Category {id: 12, name: "fenetre en verre", ...}
        }

        $tree = [];
        foreach ($categories as $category) {
            // Si Category {id: 8, ... parentId: 14} && byId[14] n'est pas null
            if ($category->parentId && isset($byId[$category->parentId])) {
                // Alors $byId[14]->childrens <= Category {id: 8, ... parent_id: 14}
                $byId[$category->parentId]->addChild($category);
            } else {
                $tree[] = $category;
            }
        }
        return $tree;
    }

    public function delete(int $id): void
    {
        $this->categoryRepo->deleteCategory($id);
    }
}
