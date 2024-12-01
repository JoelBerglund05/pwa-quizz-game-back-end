<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
     // .....
	function render($request, Throwable $exception):JsonResponse
    {
        if ($this->isHttpException($exception)) {
            if ($exception->getCode() == 404) {
                return response()->json([
                    "message" => "Page not found!",

                ], 404);
            }
            if ($exception->getCode() == 500) {
                return response()->json(["message" => "Somerhing is wrong"], 500);
            }
        }
        return parent::prepareJsonResponse($request, $exception);
    }
}
