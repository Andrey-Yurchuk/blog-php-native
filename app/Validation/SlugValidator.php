<?php

declare(strict_types=1);

namespace App\Validation;

final readonly class SlugValidator
{
    public static function isValid(string $slug): bool
    {
        return preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $slug) === 1;
    }
}
