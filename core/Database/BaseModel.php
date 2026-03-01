<?php

namespace Core\Database;

use DateTime;

abstract class BaseModel
{
    protected static function toDateTime(?string $value): ?DateTime
    {
        if ($value === null) return null;

        try {
            return new DateTime($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
