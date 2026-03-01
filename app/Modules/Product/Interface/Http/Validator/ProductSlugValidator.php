<?php
// App\Modules\Product\Validator\ProductSlugValidator

namespace App\Modules\Product\Interface\Http\Validator;

use App\Exception\ValidationException;

class ProductSlugValidator implements ProductSlugValidatorInterface
{
    public function validate(string $slug): void
    {
        $slug = self::canonical($slug);

        if (strlen($slug) < 3 || strlen($slug) > 120) {
            throw new ValidationException("Non respect de la taille entrée", "Slug");
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new ValidationException("Non respect du format entré", "Slug");
        }
    }

    public function canonical(string $slug): string
    {
        return strtolower($slug);
    }
}
