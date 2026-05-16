<?php

declare(strict_types=1);

namespace App\View;

use RuntimeException;
use Smarty\Smarty;

final class SmartyRenderer
{
    public function __construct(
        private readonly Smarty $smarty,
        private readonly string $compileDir,
    ) {
        $this->prepareCompileDir();
    }

    /**
     * Создает рендерер с каталогами шаблонов, кэша и HTML-экранированием
     */
    public static function createDefault(string $templateDir, string $compileDir): self
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($templateDir);
        $smarty->setCompileDir($compileDir);
        $smarty->setEscapeHtml(true);

        return new self($smarty, $compileDir);
    }

    /**
     * Передает данные в шаблон и возвращает готовый HTML
     */
    public function renderTemplate(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign((string) $key, $value);
        }

        return $this->smarty->fetch($template);
    }

    /**
     * Создает каталог кэша Smarty (если нету)
     */
    private function prepareCompileDir(): void
    {
        if (is_dir($this->compileDir)) {
            return;
        }

        if (!mkdir($this->compileDir, 0775, true) && !is_dir($this->compileDir)) {
            throw new RuntimeException(sprintf('Unable to create Smarty compile directory "%s".', $this->compileDir));
        }
    }
}
