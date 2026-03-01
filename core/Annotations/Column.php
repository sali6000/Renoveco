<?php

namespace Core\Annotations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name,
        public string $type = "string",
        public bool $primary = false,
        public bool $nullable = false
    ) {}
}
