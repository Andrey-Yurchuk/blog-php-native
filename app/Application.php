<?php

declare(strict_types=1);

namespace App;

use App\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\View\SmartyRenderer;
use Throwable;

final readonly class Application
{
    public function __construct(
        private Router $router,
        private SmartyRenderer $renderer,
        private bool $debug,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatchRequest($request);
        } catch (NotFoundException) {
            return $this->createNotFoundResponse();
        } catch (Throwable $exception) {
            return $this->createServerErrorResponse($exception);
        }
    }

    private function createNotFoundResponse(): Response
    {
        return Response::createHtml(
            $this->renderer->renderTemplate('errors/404.tpl', [
                'pageTitle' => 'Page not found',
            ]),
            404,
        );
    }

    private function createServerErrorResponse(Throwable $exception): Response
    {
        if ($this->debug) {
            return Response::createHtml(
                $this->renderer->renderTemplate('errors/500-debug.tpl', [
                    'pageTitle' => 'Server error',
                    'errorMessage' => $exception->getMessage(),
                    'errorType' => $exception::class,
                    'errorTrace' => $exception->getTraceAsString(),
                ]),
                500,
            );
        }

        return Response::createHtml(
            $this->renderer->renderTemplate('errors/500.tpl', [
                'pageTitle' => 'Server error',
            ]),
            500,
        );
    }
}
