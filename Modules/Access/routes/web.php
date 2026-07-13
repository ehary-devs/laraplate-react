<?php

use Illuminate\Support\Facades\Route;
use Modules\Access\Http\Controllers\PermissionController;
use Modules\Access\Http\Controllers\RoleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('access/roles', [RoleController::class, 'index'])
        ->middleware('permission:role.view')
        ->name('role.index');

    Route::get('access/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:role.create')
        ->name('role.create');

    Route::post('access/roles', [RoleController::class, 'store'])
        ->middleware('permission:role.create')
        ->name('role.store');

    Route::get('access/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:role.update')
        ->name('role.edit');

    Route::put('access/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:role.update')
        ->name('role.update');

    Route::delete('access/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:role.delete')
        ->name('role.destroy');

    Route::get('access/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permission.view')
        ->name('permission.index');
});
