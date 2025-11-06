<?php

namespace Core;

use Core\Database\QueryBuilder;
use Config\AppConfig;

use App\Services\Schema\SchemaFactory;

use App\Modules\Home\Controller\HomeController;
use App\Modules\Shared\Controller\HeaderController;

use App\Modules\About\HistoryController;
use App\Modules\About\HistoryRepository;
use App\Modules\About\HistoryService;
use App\Modules\Admin\AdminController;
use App\Modules\Auth\Controller\AuthController;
use App\Modules\Auth\Service\AuthService;
use App\Modules\Product\Controller\ProductController;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\ProductService;
use App\Modules\Product\Repository\ProductCategoryRepository;
use App\Modules\Product\Service\ProductCategoryService;
use App\Modules\User\Repository\UserRepository;


use Exception;

class ControllerFactory
{
    public static function create($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            throw new Exception("ControllerFactory n'a pas trouvé : " . $controllerClass);
        }

        $pdo = AppConfig::getDatabase(); // Obtenir les informations de connexion à la DB (hostname=***, ...)
        $queryBuilder = new QueryBuilder($pdo); // Obtenir le format des requêtes pour la DB (select, from, where, join, orderBy, ...)

        switch ($controllerClass) {
            case AdminController::class:
                $productRepo = new ProductRepository($pdo, $queryBuilder);
                $productService = new ProductService($productRepo);
                $productCategoryRepo = new ProductCategoryRepository($pdo, $queryBuilder);
                $productCategoryService = new ProductCategoryService($productCategoryRepo);
                return new AdminController($productService, $productCategoryService);
            case AuthController::class:
                $userRepo = new UserRepository($pdo, $queryBuilder);
                $authService = new AuthService($userRepo);
                return new AuthController($authService);
            case ProductController::class:
                $productRepo = new ProductRepository($pdo, $queryBuilder);
                $productService = new ProductService($productRepo);
                $schemaJson = SchemaFactory::createProductSchema();
                return new ProductController($productService, $schemaJson);
            case HomeController::class:
                $productRepo = new ProductRepository($pdo, $queryBuilder);
                $productService = new ProductService($productRepo);
                return new HomeController($productService);
            case HeaderController::class:
                $productRepo = new ProductRepository($pdo, $queryBuilder);
                $productService = new ProductService($productRepo);
                return new HeaderController(/*$productService*/);
            case HistoryController::class:
                $historyRepo = new HistoryRepository(AppConfig::getPath('PATH_LOCAL_STORAGE_DATAS') . 'history.json');
                $historyService = new HistoryService($historyRepo);
                return new HistoryController($historyService);
            default:
                return new $controllerClass(); // Pour les contrôleurs sans dépendances
        }
    }
}
