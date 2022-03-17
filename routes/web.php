<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::prefix('reservation')->middleware('auth')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/reservation_list', [ReservationController::class, 'reservation_list'])->name('reservation.reservation_list');
    Route::post('/', [ReservationController::class, 'store'])->name('reservation.store');
});