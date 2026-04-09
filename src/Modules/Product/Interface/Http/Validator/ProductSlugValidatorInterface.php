<?php

namespace Src\Modules\Product\Interface\Http\Validator;

interface ProductSlugValidatorInterface
{
    public function canonical(string $slug): string;
    public function validate(string $slug): void;
}
