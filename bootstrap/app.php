<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\LogApiRequests::class,
        ]);
        $middleware->alias([
            'staff' => \App\Http\Middleware\EnsureUserIsStaff::class,
            'customer' => \App\Http\Middleware\EnsureUserIsCustomer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        
        // 1. Model not found (e.g. GET /books/999) -> clean 404
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
        if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        // // 2. Validation errors -> 422 with field-level messages
        // $exceptions->render(function (ValidationException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'message' => 'The given data was invalid.',
        //             'errors' => $e->errors(),
        //         ], 422);
        //     }
        // });

        // 3. Auth: not logged in -> 401
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // // 4. Auth: logged in but not allowed -> 403
        // $exceptions->render(function (AuthorizationException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'message' => 'This action is unauthorized.',
        //         ], 403);
        //     }
        // });

        // // 5. DB constraint violations (our restrictOnDelete case) -> 409
        // $exceptions->render(function (QueryException $e, Request $request) {
        //     if ($request->is('api/*') && $e->getCode() === '23000') {
        //         return response()->json([
        //             'message' => 'This record cannot be deleted because it is referenced by other records.',
        //         ], 409);
        //     }
        // });

        // // 6. Catch-all fallback for anything unhandled -> 500, no stack trace leaked
        // $exceptions->render(function (\Throwable $e, Request $request) {
        //     if ($request->is('api/*') && ! app()->hasDebugModeEnabled()) {
        //         return response()->json([
        //             'message' => 'Something went wrong. Please try again later.',
        //         ], 500);
        //     }
        // });
    })->create();
