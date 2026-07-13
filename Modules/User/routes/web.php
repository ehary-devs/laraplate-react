<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:user.view')
        ->name('user.index');

    Route::get('users/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('user.create');

    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('user.store');

    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.update')
        ->name('user.edit');

    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.update')
        ->name('user.update');

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('user.destroy');
});
