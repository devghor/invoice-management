<?php

declare(strict_types=1);

namespace Modules\Base\Traits;

use Modules\Base\Enums\StatusCodeEnum;

trait HttpResponseTrait
{
    public static function successResponse($data, $message = null, $code = StatusCodeEnum::ACCEPTED, $headers = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code, $headers);
    }

    public static function errorResponse($error, $message = null, $code = StatusCodeEnum::UNPROCESSABLE_ENTITY, $headers = [])
    {
        return response()->json([
            'message' => $message,
            'error' => $error,
        ], $code, $headers);
    }
}
