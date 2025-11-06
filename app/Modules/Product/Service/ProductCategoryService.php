<?php

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\ProductCategory;
use App\Modules\Product\Repository\ProductCategoryRepository;

class ProductCategoryService
{
    public function __construct(private ProductCategoryRepository $categoryRepo) {}

    public function createCategory(array $data): ProductCategory
    {
        $category = new ProductCategory(
            $data['name'],
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['parent_id'] ?? null
        );

        $this->categoryRepo->save($category); // ⚡ Sauvegarde via Repository

        return $category; // ✅ Retourne le modèle
    }

    /**
     * Retourne toutes les catégories sous forme d'arbre hiérarchique
     */
    public function getCategoryTree(): array
    {
        // $categories = $this->categoryRepo->findAll(['products']); Permet de charger les categories mais aussi les produits s'y trouvant (ManyToMany)
        $categories = $this->categoryRepo->getCategoriesTree();
        $byId = [];

        foreach ($categories as $category) {
            $byId[$category->id] = $category;
        }

        $tree = [];
        foreach ($categories as $category) {
            if ($category->parentId) {
                $byId[$category->parentId]->addChild($category);
            } else {
                $tree[] = $category;
            }
        }
        return $tree;
    }
}
