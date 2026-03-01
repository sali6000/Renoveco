<?php

namespace Core\Annotations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation
{
    public function __construct(
        public string $type,       // "ManyToOne", "OneToMany", "ManyToMany"
        public string $target,     // classe cible
        public ?string $mappedBy = null,      // utilisé pour OneToMany
        public ?string $inversedBy = null,    // utilisé pour ManyToOne
        public ?string $pivotTable = null,    // utilisé pour ManyToMany
        public ?string $pivotTableClass = null,    // utilisé pour ManyToMany
        public ?string $joinColumn = null,    // utilisé pour ManyToMany
        public ?string $inverseJoinColumn = null, // utilisé pour ManyToMany
        public ?string $alias = null // utilisé pour ManyToMany

    ) {}
}
