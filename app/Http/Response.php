<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Response
{
    public function __construct(
        private string $content,
        private int $statusCode = 200,
        private array $headers = [],
    ) {
    }

    /**
     * Создает HTML-ответ с Content-Type
     */
    public static function createHtml(string $content, int $statusCode = 200): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Создает текстовый ответ с Content-Type
     */
    public static function createText(string $content, int $statusCode = 200): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * Отправляет HTTP-статус, заголовки и тело ответа
     */
    public function sendResponse(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->content;
    }
}
