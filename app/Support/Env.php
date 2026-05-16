<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class Env
{
    /**
     * Возвращает строковое значение переменной окружения или значение по дефолту
     */
    public static function getString(string $key, ?string $default = null): string
    {
        $value = self::getRawValue($key);

        if ($value === null || $value === '') {
            if ($default === null) {
                throw new RuntimeException(sprintf('Missing required environment variable "%s".', $key));
            }

            return $default;
        }

        return $value;
    }

    /**
     * Возвращает boolean-значение переменной окружения или значение по дефолту
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::getRawValue($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /**
     * Возвращает int-значение переменной окружения или значение по дефолту
     */
    public static function getInt(string $key, ?int $default = null): int
    {
        $value = self::getRawValue($key);

        if ($value === null || $value === '') {
            if ($default === null) {
                throw new RuntimeException(sprintf('Missing required environment variable "%s".', $key));
            }

            return $default;
        }

        $intValue = filter_var($value, FILTER_VALIDATE_INT);

        if ($intValue === false) {
            throw new RuntimeException(sprintf('Environment variable "%s" must be an integer.', $key));
        }

        return $intValue;
    }

    /**
     * Читает исходное значение переменной окружения из $_ENV, $_SERVER или getenv()
     */
    private static function getRawValue(string $key): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return null;
        }

        return (string) $value;
    }
}
