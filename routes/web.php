<?php

use App\Models\Game;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|Sme
*/
// cache clear used by the admin "Utilities > Clear Cache" menu item; admins only
Route::get('clear', function() {
    if (!auth()->check() || !auth()->user()->hasRole('admin')) {
        abort(403);
    }

    Artisan::call('optimize:clear');

    return back();
})->middleware('web');

// GAMES PROVIDER
require(__DIR__ . '/groups/provider/games.php');
require(__DIR__ . '/groups/provider/vibra.php');
require(__DIR__ . '/groups/provider/kagaming.php');
require(__DIR__ . '/groups/provider/salsa.php');


// GATEWAYS
require(__DIR__ . '/groups/gateways/bspay.php');
require(__DIR__ . '/groups/gateways/stripe.php');
require(__DIR__ . '/groups/gateways/suitpay.php');

/// SOCIAL
require(__DIR__ . '/groups/auth/social.php');

// APP
require(__DIR__ . '/groups/layouts/app.php');

