<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            $exception = new NotFoundHttpException('Страница не найдена', previous:$exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            $exception = new ModelNotFoundException('Страница не найдена', previous:$exception);
        }

        if ($exception instanceof AuthenticationException) {
            $exception = new AuthenticationException(
                'Запрос требует аутентификации.',
                $exception->guards(),
                $exception->redirectTo()
            );
        }

        if ($exception instanceof AuthorizationException) {
            $exception = new AuthorizationException(trans($exception->getMessage()), previous:$exception);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $exception = new AccessDeniedHttpException(trans($exception->getMessage()), previous:$exception);
        }
        if ($exception instanceof RequestException) {
            abort(400, $exception->getMessage());
        }

        return parent::render($request, $exception);
    }
}
