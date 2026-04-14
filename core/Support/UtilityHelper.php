<?php
// Core/Support/UtilityHelper

namespace Core\Support;


final class UtilityHelper
{

    static function getAfterDot(string $value): string
    {
        $parts = explode('.', $value);
        return end($parts);
    }
}
