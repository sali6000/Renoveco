<?php

namespace Core;

use ReflectionClass;

class Container
{
    private array $instances = [];      // singleton
    private array $bindings = [];       // interface => concrete/factory
    private array $reflectionCache = []; // ReflectionClass cache

    /**
     * Récupère une instance d'une classe ou interface
     */
    public function get(string $class)
    {
        // Déjà instancié ?
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Binding manuel ?
        if (isset($this->bindings[$class])) {

            // Bind manuel trouvé dans bootstrap.php
            $binding = $this->bindings[$class];

            // Closure ou classe concrète
            if (is_callable($binding)) {
                $instance = $binding($this);
            } else {
                $class = $binding;
            }
        }

        // Build si pas encore instancié
        $instance = $instance ?? $this->build($class);

        // Stockage singleton
        return $this->instances[$class] = $instance;
    }

    /**
     * Bind une interface ou type à une classe concrète ou une factory
     */
    public function bind(string $abstract, string|callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Build d’une classe avec autowiring + convention repository
     */
    private function build(string $class)
    {
        // Reflection cache d'un controller
        $reflection = $this->reflectionCache[$class] ?? new ReflectionClass($class);
        $this->reflectionCache[$class] = $reflection;

        // Récupération du constructeur
        $constructor = $reflection->getConstructor();

        // Si pas de constructeur, retourner la class tel quelS
        if (!$constructor) {
            return new $class;
        }

        // Définir la source de donnée lié au repository pour le binding
        $dataSource = ucfirst($_ENV['APP_DATA_SOURCE'] ?? 'Mysql');

        // Récupérer les parametres
        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {

            // Récupérer le nom de la class du paramètre
            $type = $param->getType()?->getName();

            // Si le nom du paramètre renvoit null, levé une exception
            if (!$type) {
                throw new \Exception("Paramètre non typé \${$param->getName()} dans $class");
            }

            // Si le paramètre est une interface, et qu'il se trouve dans 'Domain\Repository'
            if (interface_exists($type) && str_contains($type, 'Domain\Repository')) {

                // Remplacer l'interface par la source des données (convention)
                $type = str_replace(
                    ['Domain\Repository', 'Interface'],
                    ["Infrastructure\\Persistence\\$dataSource", $dataSource],
                    $type
                );
            }

            // Récursif pour classes / interfaces
            $dependencies[] = $this->get($type);
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
