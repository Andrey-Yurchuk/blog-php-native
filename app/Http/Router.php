<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\NotFoundException;
use Closure;

final class Router
{
    private array $routes = [];

    /**
     * Регистрирует GET-маршрут и его обработчик
     */
    public function addGet(string $pattern, Closure $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * Находит подходящий маршрут и возвращает ответ обработчика
     */
    public function dispatchRequest(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            $params = $this->matchRoute($route['pattern'], $request->getPath());

            if ($params !== null) {
                return $route['handler']($request, $params);
            }
        }

        throw new NotFoundException('Page not found.');
    }

    /**
     * Проверяет путь по шаблону маршрута и возвращает параметры
     */
    private function matchRoute(string $pattern, string $path): ?array
    {
        [$regex, $paramNames] = $this->buildRegex($pattern);

        if (preg_match('#^' . $regex . '$#', $path, $matches) !== 1) {
            return null;
        }

        return $this->extractParams($paramNames, $matches);
    }

    /**
     * Собирает регулярное выражение маршрута и имена параметров
     */
    private function buildRegex(string $pattern): array
    {
        $paramNames = [];
        $parts = preg_split(
            '#(\{[a-zA-Z_][a-zA-Z0-9_]*})#',
            $pattern,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY,
        );

        if ($parts === false) {
            return [preg_quote($pattern, '#'), []];
        }

        $regex = '';

        foreach ($parts as $part) {
            if (preg_match('#^\{([a-zA-Z_][a-zA-Z0-9_]*)}$#', $part, $matches) === 1) {
                $paramNames[] = $matches[1];
                $regex .= '([^/]+)';

                continue;
            }

            $regex .= preg_quote($part, '#');
        }

        return [$regex, $paramNames];
    }

    /**
     * Извлекает параметры из совпадений маршрута
     */
    private function extractParams(array $paramNames, array $matches): array
    {
        array_shift($matches);
        $params = [];

        foreach ($paramNames as $index => $name) {
            $params[$name] = rawurldecode($matches[$index] ?? '');
        }

        return $params;
    }
}
