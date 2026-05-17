<?php

declare(strict_types=1);

namespace App\Validation;

final readonly class ImagePathValidator
{
    private const string BASE_PATH = '/assets/images/';
    private const string FALLBACK_PATH = '/assets/images/php-card.svg';

    public static function normalize(string $path): string
    {
        $path = trim($path);

        if (preg_match('/\A[a-zA-Z0-9._-]+\z/', $path) === 1) {
            return self::BASE_PATH . $path;
        }

        if (preg_match('#\A/assets/images/[a-zA-Z0-9._-]+\z#', $path) === 1) {
            return $path;
        }

        return self::FALLBACK_PATH;
    }
}
