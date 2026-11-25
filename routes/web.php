<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
Route::get('/migrate-fresh', function () {
    Artisan::call('migrate:fresh', [
        '--force' => true,
    ]);

    return 'Database tables dropped and migrations re-run successfully.';
});

Route::get('/', function () {
    return view('welcome');
});

