<?php
// Src\Modules\Product\Validator\ProductSlugValidator

namespace Src\Modules\Product\Interface\Http\Validator;

final class ProductSlugValidator
{
    public function validate(string $slug): bool
    {
        $slug = self::canonical($slug);

        if (strlen($slug) < 3 || strlen($slug) > 120) {
            return false;
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            return false;
        }

        return true;
    }

    public function canonical(string $slug): string
    {
        return strtolower($slug);
    }
}
