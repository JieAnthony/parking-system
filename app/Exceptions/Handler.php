<?php

namespace App\Exceptions;

use App\Enums\CodeEnum;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        BusinessException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenInvalidException::class,
        TokenExpiredException::class,
        UnauthorizedHttpException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $request = request();
        $this->renderable(function (BusinessException $e) use ($request) {
            if ($request->expectsJson()) {
                return $this->response()->fail($e->getMessage(), $e->getCode());
            }
        });

        $this->renderable(function (ValidationException $e) use ($request) {
            if ($request->expectsJson()) {
                return $this->response()->fail($e->validator->errors()->first(), CodeEnum::VALIDATION_ERROR);
            }
        });

        $this->renderable(function (HttpException $e) use ($request) {
            if ($request->expectsJson()) {
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return $this->response()->fail('data not found', CodeEnum::MODEL_NOT_FOUND);
                }

                return $this->response()->formatData($e->getMessage(), $e->getStatusCode(), null, $e->getStatusCode());
            }
        });
    }
}
