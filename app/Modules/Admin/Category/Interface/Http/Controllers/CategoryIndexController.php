<?php

namespace App\Modules\Admin\Category\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Exception\ServiceException;
use App\Exception\UniqueConstraintException;
use App\Exception\ValidationException;
use App\Modules\Category\Domain\Entity\Category;
use Core\BaseController;
use App\Modules\Category\Domain\Service\CategoryService;
use Core\Logger\AccessLogger;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Core\Support\ResponseHelper;
use Throwable;

#[Route('/admin/category')]
class CategoryIndexController extends BaseController
{
    public function __construct(
        private CategoryService $categoryService,
    ) {
        parent::__construct('Admin/Category');
    }

    #[Route('', methods: ['GET'])]
    public function index(): void
    {
        $this->set('categories', $this->categoryService->getCategoriesTree());
        $this->render();
    }


    #[Route('getCategoriesJson', methods: ['GET'])]
    public function getCategoriesJson(): void
    {
        header('Content-Type: application/json');

        $categories = $this->categoryService->getCategories(); // même méthode que ton index

        $data = [];

        $data = array_map(fn($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'description' => $cat->description,
            'parent_id' => $cat->parentId
        ], $categories);

        echo json_encode($data);
        exit;
    }

    /*************************************************************************************************
     * 
     * Création d'une catégorie via formulaire ou API JSON
     * 
     *************************************************************************************************/
    #[Route('addCategoryPost', methods: ['POST'])]
    public function addCategoryPost()
    {
        try {
            $this->handleCategoryCreation($_POST);

            $categories = $this->categoryService->getCategoriesTree();
            $this->set('categories', $categories);

            $this->render();
        } catch (\Throwable $e) {
            $this->handleErrorTwig($e);
        }
    }

    #[Route('addCategoryJson', methods: ['POST'])]
    public function addCategoryJson()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->handleCategoryCreation($data);

            return ResponseHelper::success("Catégorie créée avec succès");
        } catch (\Throwable $e) {
            return $this->handleErrorJson($e);
        }
    }

    private function handleCategoryCreation(array $data): Category
    {
        // Sanitization
        $name        = SecurityHelper::sanitizeString($data['name'] ?? null, "name category", maxLength: 100);
        $slug        = SecurityHelper::sanitizeString($data['slug'] ?? null, "slug category", maxLength: 100);
        $description = SecurityHelper::sanitizeString($data['description'] ?? null, "description category", canBeEmpty: true, maxLength: 500);
        $parent_id   = SecurityHelper::sanitizeInt($data['parent_id'] ?? null, "parent_id category", canBeNull: true);

        // Entité
        $category = new Category(
            $name,
            $slug,
            $description,
            null,
            $parent_id !== 0 ? $parent_id : null
        );

        // Persistance
        $this->categoryService->createCategory($category);

        return $category;
    }

    private function handleErrorTwig(\Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $this->setFlash('error', "Erreur de validation : " . $e->getMessage());
        } elseif ($e instanceof UniqueConstraintException) {
            $this->setFlash('error', "Ce champ est déjà enregistré : " . $e->getField());
        } elseif ($e instanceof ServiceException) {
            $this->setFlash('error', "Erreur du service (Code : " . $e->getErrorId() . ")");
        } else {
            $errorId = uniqid('admin_ctrl_', true);
            AccessLogger::log("Erreur inconnue ($errorId) : " . $e, AccessLogger::LEVEL_ERROR);
            $this->setFlash('error', "Erreur inconnue (Code: $errorId)");
        }

        $this->redirect('/admin/categories');
    }

    private function handleErrorJson(\Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return ResponseHelper::error("Erreur de validation : " . $e->getMessage());
        }
        if ($e instanceof UniqueConstraintException) {
            return ResponseHelper::error("Ce champ est déjà enregistré : " . $e->getField());
        }
        if ($e instanceof ServiceException) {
            return ResponseHelper::error("Erreur du service (Code : " . $e->getErrorId() . ")");
        }

        $errorId = uniqid('admin_ctrl_', true);
        AccessLogger::log("Erreur inconnue ($errorId) : " . $e, AccessLogger::LEVEL_ERROR);
        return ResponseHelper::error("Erreur inconnue (Code: $errorId)");
    }

    #[Route('deleteCategory', methods: ['GET'])]
    public function deleteCategory(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifier que l'id est bien présent et numérique
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID invalide']);
            return;
        }

        $id = (int) $data['id'];

        // Appel du service métier
        $this->categoryService->delete($id);

        // Réponse JSON de confirmation
        ResponseHelper::success("Suppression de la categorie {$id} avec succès");
    }
}
