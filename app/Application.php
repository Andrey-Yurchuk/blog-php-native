<?php

declare(strict_types=1);

namespace App;

use App\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use Throwable;

final readonly class Application
{
    public function __construct(
        private Router $router,
        private bool $debug,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException) {
            return Response::text('Page not found', 404);
        } catch (Throwable $exception) {
            if ($this->debug) {
                return Response::text($exception->getMessage(), 500);
            }

            return Response::text('Server error', 500);
        }
    }
}
