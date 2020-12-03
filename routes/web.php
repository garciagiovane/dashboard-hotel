<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\QuartoController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

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
    return view('layout.layout');
});

Route::resource('customers', CustomerController::class);
Route::resource('reservations', ReservationController::class)->except([
    'destroy',
    'update',
    'edit',
    'show',
    'index'
]);
Route::resource('quartos', QuartoController::class);
Route::resource('servicos', ServicoController::class);
Route::get('/quartos/{codigo}/manutencao/encerrar', [QuartoController::class, 'liberarQuarto']);

Route::get('reservations/checkin', [ReservationController::class, 'showcheckin']);
Route::patch('reservations/checkin', [ReservationController::class, 'showReservationsCheckin']);
Route::get('reservations/checkin/{reserva}', [ReservationController::class, 'efetuarCheckin']);

Route::get('reservations/checkout', [ReservationController::class, 'showcheckout']);
Route::patch('reservations/checkout', [ReservationController::class, 'docheckout']);

Route::get('reservations/payment/{reserva}', [ReservationController::class, 'payment']);
Route::patch('reservations/payment', [ReservationController::class, 'doPayment']);