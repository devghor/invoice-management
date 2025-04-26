<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::prefix(config('base.api_version').'/'.config('base.module_prefix.user'))->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
