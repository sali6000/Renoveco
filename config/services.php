<?php

use Src\Modules\Product\Interface\Http\Validator\ProductSlugValidator;
use Src\Modules\Product\Interface\Http\Validator\ProductSlugValidatorInterface;
use Config\PdoConnection;
use Core\Database\QueryBuilder;
use Core\Database\QueryBuilderInterface;
use Src\Modules\Auth\Domain\Repository\LoginAttemptRepositoryInterface;
use Src\Modules\Auth\Infrastructure\Persistence\Mysql\LoginAttemptRepositoryMysql;

// (Lié à Core/Container.php - Anciennement ControllerFactory) 
// Sert à binder manuelement les dépendances qui ne proviennent pas de Domain\Repository

// --------------------- Bindings manuels -----------------------
// --- Slug ---
$container->bind(ProductSlugValidatorInterface::class, ProductSlugValidator::class);
// --- Auth ---
$container->bind(LoginAttemptRepositoryInterface::class, LoginAttemptRepositoryMysql::class);


// Binding PDO
$container->bind(PDO::class, function () {
    return PdoConnection::connection();
});

// Binding QueryBuilderInterface
$container->bind(QueryBuilderInterface::class, function ($c) {
    return new QueryBuilder($c->get(PDO::class));
});

// Paramètres primitifs
$container->bind('string', function () {
    return $_ENV['APP_SOME_STRING'] ?? '';
});
