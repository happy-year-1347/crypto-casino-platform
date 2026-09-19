<?php


use App\Http\Controllers\Layouts\ApplicationController;
use Illuminate\Support\Facades\Route;

// Explicitly define game routes to prevent Filament admin redirect
Route::get('games/play/{id}/{slug}', ApplicationController::class);

Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
