<?php

use Config\PdoConnection;
use Core\Database\QueryBuilder;
use Core\Database\QueryBuilderInterface;

// (Lié à Core/Container.php - Anciennement ControllerFactory) 
// Sert à binder manuelement les dépendances qui ne proviennent pas de Domain\Repository

// --------------------- Bindings manuels -----------------------
// Binding PDO
$container->bind(PDO::class, function () {
    return PdoConnection::connection();
});

// Binding QueryBuilderInterface
$container->bind(QueryBuilderInterface::class, function ($c) {
    return new QueryBuilder($c->get(PDO::class));
});
