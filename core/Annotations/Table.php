<?php

namespace Core\Annotations;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        public string $name,
        public string $alias
    ) {}
}
