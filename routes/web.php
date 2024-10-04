<?php

use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
// use Exception;
use Twilio\Rest\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['middleware' => 'cors'], function () {
    Route::view('/', 'welcome');

    Route::view('/my-tags', 'my-tags')
        ->middleware(['auth', 'verified', 'web'])
        ->name('my-tags');

    Route::view('/profile', 'profile')
        ->middleware(['auth', 'web'])
        ->name('profile');

    Route::view('/create-tag', 'create-tag')
        ->middleware(['web'])
        ->name('create-tag');

    Route::view('/create-bulk-tags', 'create-bulk-tags')
        ->middleware(['web'])
        ->name('create-bulk-tags');

    Route::view('/t/{uid}', 'view-tag')
        ->middleware(['web'])
        ->name('view-tag');

    Route::view('/print/{uid}', 'print-tag')
        ->middleware(['web'])
        ->name('print-tag');

    Route::view('/scan', 'scan-qrcode')
        ->middleware(['web'])
        ->name('scan-tag');
});
require __DIR__ . '/auth.php';
