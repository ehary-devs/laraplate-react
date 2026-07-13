<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

// The `dashboard` route is owned by Modules\Dashboard (auth + verified).

require __DIR__.'/settings.php';
