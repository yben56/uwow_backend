<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {

            // 403
            if ($e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Forbidden.'
                ], 403);
            }

            // 404
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Route not found.'
                ], 404);
            }

            // 405
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 405,
                    'message' => 'HTTP Method not allowed.'
                ], 405);
            }

            // 500
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error.',
                'error_detail' => app()->isLocal() ? $e->getMessage() : null
            ], 500);

            // Others
            if ($e instanceof HttpException) {
                return response()->json([
                    'status' => $e->getStatusCode(),
                    'message' => $e->getMessage()
                ], $e->getStatusCode());
            }  
        });
    })->create();
